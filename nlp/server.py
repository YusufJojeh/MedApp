#!/usr/bin/env python3
"""
Medical Voice Assistant – Flask NLP Inference Service
Provides intent prediction, medical Q&A, health tips, and doctor suggestions
Enhanced with comprehensive health tips and medical knowledge.
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import pandas as pd
import numpy as np
import json
import logging
from pathlib import Path
from typing import Dict, List, Optional
from datetime import datetime

# Custom JSON encoder to handle NumPy types and Python scalars
class NumpyEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, np.integer):
            return int(obj)
        elif isinstance(obj, np.floating):
            return float(obj)
        elif isinstance(obj, np.ndarray):
            return obj.tolist()
        elif hasattr(obj, "item"):
            return obj.item()
        return super().default(obj)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    handlers=[
        logging.FileHandler("logs/assistant.log", encoding="utf-8"),
        logging.StreamHandler(),
    ],
)
logger = logging.getLogger(__name__)

# Create app and set custom JSON provider/encoder
app = Flask(__name__)
try:
    from flask.json.provider import DefaultJSONProvider

    class CustomJSONProvider(DefaultJSONProvider):
        def dumps(self, obj, **kwargs):
            kwargs.setdefault("cls", NumpyEncoder)
            return json.dumps(obj, **kwargs)

        def loads(self, s: str | bytes, **kwargs):
            return json.loads(s, **kwargs)

    app.json = CustomJSONProvider(app)
except Exception:
    app.json_encoder = NumpyEncoder

# Enable CORS for requests from the Laravel frontend (port 8000)
CORS(
    app,
    resources={r"/*": {"origins": ["http://127.0.0.1:8000", "http://localhost:8000", "http://localhost:3000"]}},
    methods=["GET", "POST", "OPTIONS"],
    allow_headers=["Content-Type", "Authorization", "Accept", "Origin"],
    supports_credentials=False,
    expose_headers=["Content-Type", "Authorization"],
)

# Global variables for models and data
intent_model: Optional[object] = None
vectorizer: Optional[object] = None
qa_model: Optional[object] = None
qa_vectorizer: Optional[object] = None
medquad_data: Optional[pd.DataFrame] = None
doctor_data: Optional[pd.DataFrame] = None
specialties_data: Optional[pd.DataFrame] = None
medical_knowledge: Optional[dict] = None

# Health tips database
HEALTH_TIPS: Dict[str, List[str]] = {
    "general": [
        "Stay hydrated by drinking 8-10 glasses of water daily",
        "Get 7-9 hours of quality sleep each night",
        "Exercise regularly (30 minutes daily)",
        "Eat a balanced diet with fruits and vegetables",
        "Schedule regular check-ups with your doctor",
        "Practice stress management techniques",
        "Maintain good posture throughout the day",
        "Limit processed foods and added sugars",
        "Get regular dental check-ups",
        "Practice good hand hygiene",
    ],
    "cardiology": [
        "Monitor your blood pressure regularly",
        "Eat heart-healthy foods (omega-3, fiber)",
        "Exercise for at least 150 minutes weekly",
        "Quit smoking and avoid secondhand smoke",
        "Limit salt intake to less than 2,300mg daily",
        "Maintain a healthy weight",
        "Manage stress through meditation or yoga",
        "Get regular cholesterol screenings",
        "Limit alcohol consumption",
        "Know your family heart disease history",
    ],
    "dermatology": [
        "Use sunscreen with SPF 30+ daily",
        "Moisturize your skin regularly",
        "Avoid excessive sun exposure",
        "Check your skin for unusual moles",
        "Use gentle, fragrance-free skincare products",
        "Stay hydrated for healthy skin",
        "Avoid hot showers that dry out skin",
        "Eat foods rich in vitamins A, C, and E",
        "Get adequate sleep for skin repair",
        "Manage stress to prevent skin conditions",
    ],
    "neurology": [
        "Exercise regularly to improve brain health",
        "Eat brain-boosting foods (fish, nuts, berries)",
        "Get adequate sleep for memory consolidation",
        "Practice mental exercises and puzzles",
        "Manage stress through relaxation techniques",
        "Stay socially active and engaged",
        "Protect your head from injuries",
        "Limit alcohol consumption",
        "Monitor blood pressure and cholesterol",
        "Stay mentally active with learning new skills",
    ],
    "pediatrics": [
        "Ensure children get adequate sleep (9-12 hours)",
        "Provide balanced nutrition with regular meals",
        "Encourage physical activity daily",
        "Limit screen time to 2 hours maximum",
        "Schedule regular pediatric check-ups",
        "Keep vaccinations up to date",
        "Practice good hygiene habits",
        "Create a safe home environment",
        "Encourage reading and learning",
        "Monitor growth and development milestones",
    ],
    "orthopedics": [
        "Maintain good posture while sitting and standing",
        "Exercise regularly to strengthen muscles",
        "Use proper lifting techniques",
        "Wear supportive footwear",
        "Stretch before and after exercise",
        "Maintain a healthy weight",
        "Use ergonomic furniture and equipment",
        "Take breaks during long periods of sitting",
        "Strengthen core muscles for back support",
        "Avoid repetitive strain movements",
    ],
    "psychiatry": [
        "Practice regular self-care activities",
        "Maintain a consistent sleep schedule",
        "Exercise regularly for mental health",
        "Connect with friends and family",
        "Practice mindfulness or meditation",
        "Limit alcohol and avoid drugs",
        "Seek professional help when needed",
        "Set realistic goals and expectations",
        "Practice gratitude and positive thinking",
        "Engage in hobbies and activities you enjoy",
    ],
    "ophthalmology": [
        "Get regular eye exams every 1-2 years",
        "Protect eyes from UV rays with sunglasses",
        "Follow the 20-20-20 rule (20 min screen, 20 ft look, 20 sec break)",
        "Eat foods rich in vitamins A, C, and E",
        "Maintain proper lighting when reading",
        "Avoid rubbing your eyes",
        "Use proper contact lens hygiene",
        "Monitor for vision changes",
        "Take breaks from digital screens",
        "Keep eyes moisturized in dry environments",
    ],
    "dentistry": [
        "Brush teeth twice daily with fluoride toothpaste",
        "Floss daily to remove plaque between teeth",
        "Visit dentist every 6 months for check-ups",
        "Limit sugary foods and drinks",
        "Use mouthwash for additional protection",
        "Replace toothbrush every 3-4 months",
        "Drink fluoridated water",
        "Avoid tobacco products",
        "Eat calcium-rich foods for strong teeth",
        "Consider dental sealants for children",
    ],
    "nutrition": [
        "Eat a variety of colorful fruits and vegetables",
        "Choose whole grains over refined grains",
        "Include lean protein in every meal",
        "Limit added sugars and processed foods",
        "Stay hydrated with water throughout the day",
        "Practice portion control",
        "Eat slowly and mindfully",
        "Plan meals ahead of time",
        "Read nutrition labels carefully",
        "Consult a registered dietitian for personalized advice",
    ],
}

def load_models() -> bool:
    """Load joblib models from the 'models' folder."""
    global intent_model, vectorizer, qa_model, qa_vectorizer
    models_dir = Path("models")
    loaded_any = False

    try:
        if (models_dir / "intent_model.joblib").exists():
            intent_model = joblib.load(models_dir / "intent_model.joblib")
            logger.info("✅ Loaded intent classification model")
            loaded_any = True
        else:
            logger.warning("⚠️ Intent model not found")

        if (models_dir / "vectorizer.joblib").exists():
            vectorizer = joblib.load(models_dir / "vectorizer.joblib")
            logger.info("✅ Loaded intent vectorizer")
            loaded_any = True
        else:
            logger.warning("⚠️ Intent vectorizer not found")

        if (models_dir / "medical_qa_model.joblib").exists():
            qa_model = joblib.load(models_dir / "medical_qa_model.joblib")
            logger.info("✅ Loaded medical Q&A model")
            loaded_any = True
        else:
            logger.warning("⚠️ Medical Q&A model not found")

        if (models_dir / "medical_qa_vectorizer.joblib").exists():
            qa_vectorizer = joblib.load(models_dir / "medical_qa_vectorizer.joblib")
            logger.info("✅ Loaded medical Q&A vectorizer")
            loaded_any = True
        else:
            logger.warning("⚠️ Medical Q&A vectorizer not found")

    except Exception as e:
        logger.error(f"❌ Error loading models: {e}")
        return False

    return loaded_any

def load_data() -> bool:
    """Load CSV and JSON data from the 'data' folder."""
    global medquad_data, doctor_data, specialties_data, medical_knowledge
    data_dir = Path("data")
    loaded_any = False

    try:
        if (data_dir / "medquad.csv").exists():
            medquad_data = pd.read_csv(data_dir / "medquad.csv")
            logger.info(f"✅ Loaded MedQuAD data: {len(medquad_data)} entries")
            loaded_any = True
        else:
            logger.warning("⚠️ MedQuAD data not found")

        if (data_dir / "doctor_names.csv").exists():
            doctor_data = pd.read_csv(data_dir / "doctor_names.csv")
            logger.info(f"✅ Loaded doctor data: {len(doctor_data)} entries")
            loaded_any = True
        else:
            logger.warning("⚠️ Doctor data not found")

        if (data_dir / "specialties_ar_en.csv").exists():
            specialties_data = pd.read_csv(data_dir / "specialties_ar_en.csv")
            logger.info(f"✅ Loaded specialties data: {len(specialties_data)} entries")
            loaded_any = True
        else:
            logger.warning("⚠️ Specialties data not found")

        if (data_dir / "medical_knowledge.json").exists():
            with open(data_dir / "medical_knowledge.json", "r", encoding="utf-8") as f:
                medical_knowledge = json.load(f)
            logger.info(
                f"✅ Loaded medical knowledge: {len(medical_knowledge)} entries"
            )
            loaded_any = True
        else:
            logger.warning("⚠️ Medical knowledge not found")

    except Exception as e:
        logger.error(f"❌ Error loading data: {e}")
        return False

    return loaded_any

def extract_specialty_hint(text: str) -> Optional[str]:
    """Extract a possible medical specialty from the input text using specialties_data and keyword patterns."""
    text_lower = text.lower()

    # First check specialties_data
    if specialties_data is not None:
        for _, row in specialties_data.iterrows():
            if pd.notna(row.get("name_en")):
                if str(row["name_en"]).lower() in text_lower:
                    return row["name_en"]
            if pd.notna(row.get("name_ar")):
                if str(row["name_ar"]).strip() in text:
                    return row["name_en"] if pd.notna(row.get("name_en")) else row["name_ar"]

    # Then check specialty keyword patterns
    specialty_patterns = {
        "Cardiology": ["heart", "cardiac", "cardiovascular", "blood pressure", "cholesterol", "cardiologist"],
        "Dermatology": ["skin", "dermatology", "acne", "rash", "sun protection", "dermatologist"],
        "Neurology": ["brain", "neurology", "headache", "migraine", "memory", "neurologist"],
        "Pediatrics": ["child", "pediatric", "baby", "kids", "children", "pediatrician"],
        "Orthopedics": ["bone", "joint", "orthopedic", "back pain", "muscle", "orthopedist"],
        "Psychiatry": ["mental", "psychiatry", "anxiety", "depression", "stress", "psychiatrist"],
        "Ophthalmology": ["eye", "vision", "ophthalmology", "glasses", "contact", "ophthalmologist", "eye doctor"],
        "Dentistry": ["dental", "tooth", "teeth", "oral", "gum", "dentist"],
        "Nutrition": ["diet", "nutrition", "food", "eating", "meal"],
    }

    for specialty, keywords in specialty_patterns.items():
        if any(keyword in text_lower for keyword in keywords):
            return specialty

    return None

def predict_intent(text: str) -> Dict:
    """Predict user intent using keyword heuristics or a trained model."""
    try:
        text_lower = text.lower()

        # Check for appointment booking keywords first (highest priority)
        appointment_keywords = [
            "appointment", "book", "schedule", "booking", "reserve",
            "make appointment", "book appointment", "schedule appointment",
            "book with", "schedule with", "make booking", "reserve appointment"
        ]
        if any(keyword in text_lower for keyword in appointment_keywords):
            return {
                "intent": "book_appointment",
                "confidence": 0.95,
                "specialty_hint": extract_specialty_hint(text),
            }

        health_keywords = [
            "health tips",
            "health advice",
            "wellness tips",
            "healthy lifestyle",
            "nutrition tips",
            "exercise advice",
            "diet tips",
            "wellness advice",
            "healthy habits",
            "prevention tips",
            "health recommendations",
        ]
        if any(keyword in text_lower for keyword in health_keywords):
            return {
                "intent": "health_tips",
                "confidence": 0.95,
                "specialty_hint": extract_specialty_hint(text),
            }

        specialty_patterns = {
            "cardiology": ["heart", "cardiac", "cardiovascular", "blood pressure", "cholesterol", "cardiologist"],
            "dermatology": ["skin", "dermatology", "acne", "rash", "sun protection", "dermatologist"],
            "neurology": ["brain", "neurology", "headache", "migraine", "memory", "neurologist"],
            "pediatrics": ["child", "pediatric", "baby", "kids", "children", "pediatrician"],
            "orthopedics": ["bone", "joint", "orthopedic", "back pain", "muscle", "orthopedist"],
            "psychiatry": ["mental", "psychiatry", "anxiety", "depression", "stress", "psychiatrist"],
            "ophthalmology": ["eye", "vision", "ophthalmology", "glasses", "contact", "ophthalmologist", "eye doctor"],
            "dentistry": ["dental", "tooth", "teeth", "oral", "gum", "dentist"],
            "nutrition": ["diet", "nutrition", "food", "eating", "meal"],
        }
        for specialty, keywords in specialty_patterns.items():
            if any(keyword in text_lower for keyword in keywords):
                return {
                    "intent": "health_tips",
                    "confidence": 0.90,
                    "specialty_hint": specialty.title(),
                }

        # Use trained model only if no keyword matches are found
        if intent_model is not None and vectorizer is not None:
            try:
                text_vec = vectorizer.transform([text])
                intent_pred = intent_model.predict(text_vec)[0]
                confidence = 0.0
                if hasattr(intent_model, "predict_proba"):
                    proba = intent_model.predict_proba(text_vec)[0]
                    confidence = float(np.max(proba))
                else:
                    confidence = 0.8

                # Only use model prediction if no keyword patterns matched
                return {
                    "intent": intent_pred,
                    "confidence": confidence,
                    "specialty_hint": extract_specialty_hint(text),
                }
            except Exception as e:
                logger.error(f"Error using trained model: {e}")

        # Check for doctor-related keywords (lower priority)
        doctor_keywords = [
            "doctor", "specialist", "find", "look for", "search for",
            "need a", "want a", "see a", "consultation with", "visit", "meet"
        ]
        if any(keyword in text_lower for keyword in doctor_keywords):
            return {
                "intent": "search_doctors",
                "confidence": 0.95,
                "specialty_hint": extract_specialty_hint(text),
            }


        elif "?" in text or any(
            word in text_lower for word in ["what", "how", "why", "when", "where", "symptoms", "treatment"]
        ):
            return {
                "intent": "medical_inquiry",
                "confidence": 0.75,
                "specialty_hint": extract_specialty_hint(text),
            }

        return {
            "intent": "general_inquiry",
            "confidence": 0.60,
            "specialty_hint": extract_specialty_hint(text),
        }

    except Exception as e:
        logger.error(f"Error predicting intent: {e}")
        return {
            "intent": "unknown",
            "confidence": 0.0,
            "specialty_hint": extract_specialty_hint(text),
        }

def get_health_tips(specialty: Optional[str] = None, count: int = 5) -> Dict:
    """Return a list of health tips for a given specialty or general tips."""
    try:
        if specialty:
            lower = specialty.lower()
            mapping = {
                "cardiology": "cardiology",
                "heart": "cardiology",
                "cardiac": "cardiology",
                "cardiovascular": "cardiology",
                "dermatology": "dermatology",
                "skin": "dermatology",
                "neurology": "neurology",
                "brain": "neurology",
                "pediatrics": "pediatrics",
                "child": "pediatrics",
                "children": "pediatrics",
                "orthopedics": "orthopedics",
                "bone": "orthopedics",
                "joint": "orthopedics",
                "psychiatry": "psychiatry",
                "mental": "psychiatry",
                "ophthalmology": "ophthalmology",
                "eye": "ophthalmology",
                "vision": "ophthalmology",
                "dentistry": "dentistry",
                "dental": "dentistry",
                "tooth": "dentistry",
                "nutrition": "nutrition",
                "diet": "nutrition",
                "food": "nutrition",
            }
            cat = mapping.get(lower, "general")
            if cat in HEALTH_TIPS:
                tips = HEALTH_TIPS[cat]
                selected = tips[:count] if len(tips) >= count else tips
                return {
                    "tips": selected,
                    "specialty": specialty,
                    "category": cat,
                    "count": len(selected),
                    "source": "specialty_tips",
                }

        general_tips = HEALTH_TIPS.get("general", [])
        selected = general_tips[:count] if len(general_tips) >= count else general_tips
        return {
            "tips": selected,
            "specialty": "General Health",
            "category": "general",
            "count": len(selected),
            "source": "general_tips",
        }

    except Exception as e:
        logger.error(f"Error getting health tips: {e}")
        return {
            "tips": ["Stay hydrated and get regular exercise."],
            "specialty": "General",
            "category": "general",
            "count": 1,
            "source": "fallback",
        }

def answer_qa(question: str) -> Dict:
    """Answer a medical question using MedQuAD data or the knowledge base."""
    try:
        question_lower = question.lower()

        # Return health tips if explicitly asked
        if any(
            keyword in question_lower
            for keyword in ["health tips", "health advice", "wellness tips", "healthy lifestyle"]
        ):
            speciality = extract_specialty_hint(question)
            tips_result = get_health_tips(speciality, 5)
            answer_lines = [f"Here are some health tips for {tips_result['specialty']}:", ""]
            for i, tip in enumerate(tips_result["tips"], 1):
                answer_lines.append(f"{i}. {tip}")
            answer_lines.append(
                "\nRemember to consult with a healthcare professional for personalized advice."
            )
            answer_text = "\n".join(answer_lines)
            return {
                "answer": answer_text,
                "retrieved": [{"question": question, "answer": answer_text, "score": 1.0}],
                "source": "health_tips",
                "tips_data": tips_result,
            }

        # Try MedQuAD retrieval
        if medquad_data is not None and not medquad_data.empty:
            best_match = None
            best_score = 0.0
            question_words = set(question_lower.split())
            for _, row in medquad_data.iterrows():
                if pd.notna(row.get("question")):
                    q_lower = str(row["question"]).lower()
                    q_words = set(q_lower.split())
                    overlap = question_words.intersection(q_words)
                    score = len(overlap) / max(len(question_words), 1)
                    if score > best_score and score > 0.1:
                        best_score = float(score)
                        best_match = row
            if best_match is not None:
                return {
                    "answer": str(best_match.get("answer", "No answer available")),
                    "retrieved": [
                        {
                            "question": str(best_match.get("question", "")),
                            "answer": str(best_match.get("answer", "")),
                            "score": best_score,
                        }
                    ],
                    "source": "medquad",
                }

        # Fallback to JSON knowledge
        if medical_knowledge and isinstance(medical_knowledge, dict):
            for condition, info in medical_knowledge.get("medical_conditions", {}).items():
                if condition.lower() in question_lower:
                    symptoms = ", ".join(info.get("symptoms", []))
                    specialist = info.get("specialist", "general practitioner")
                    answer = (
                        f"For {condition.title()}, common symptoms include: {symptoms}. "
                        f"It's recommended to consult a {specialist} for proper diagnosis and treatment."
                    )
                    return {
                        "answer": answer,
                        "retrieved": [{"question": question, "answer": answer, "score": 0.8}],
                        "source": "medical_knowledge",
                    }

        return {
            "answer": "I don't have specific information about that. Please consult a healthcare professional for personalized advice.",
            "retrieved": [],
            "source": "fallback",
        }

    except Exception as e:
        logger.error(f"Error answering Q&A: {e}")
        return {
            "answer": "Sorry, I encountered an error while processing your question. Please consult a healthcare professional.",
            "retrieved": [],
            "source": "error",
        }

def suggest_doctors(specialty: str) -> Dict:
    """Suggest doctors based on a specialty."""
    try:
        if doctor_data is None:
            return {"doctors": [], "specialty": specialty, "count": 0}

        # Handle "all" specialty request
        if specialty.lower() == "all":
            filtered = doctor_data.head(10)  # Return first 10 doctors from all specialties
            return {
                "doctors": [row.to_dict() for _, row in filtered.iterrows()],
                "specialty": "All Specialties",
                "matched_specialty": "all",
                "count": len(filtered),
            }

        lower = specialty.lower()
        mapping = {
            "cardiology": ["cardiology", "cardiovascular", "heart", "cardiac"],
            "dermatology": ["dermatology", "skin", "dermatologist"],
            "neurology": ["neurology", "brain", "neurologist"],
            "pediatrics": ["pediatrics", "pediatric", "child", "children"],
            "orthopedics": ["orthopedics", "orthopedic", "bone", "joint"],
            "psychiatry": ["psychiatry", "psychiatric", "mental", "psychologist"],
            "ophthalmology": ["ophthalmology", "eye", "vision", "ophthalmologist"],
            "dentistry": ["dentistry", "dental", "dentist", "tooth"],
            "internal medicine": ["internal medicine", "general", "family medicine"],
            "obstetrics": ["obstetrics", "gynecology", "obgyn", "pregnancy"],
            "surgery": ["surgery", "surgeon", "surgical"],
        }
        matched = None
        for canonical, keywords in mapping.items():
            if any(keyword in lower for keyword in keywords):
                matched = canonical
                break

        if "specialty" in doctor_data.columns:
            if matched:
                mask = doctor_data["specialty"].str.lower().str.contains(matched, na=False)
            else:
                mask = doctor_data["specialty"].str.lower().str.contains(lower, na=False)
            filtered = doctor_data[mask]
        else:
            filtered = doctor_data

        doctors_list = []
        for _, row in filtered.iterrows():
            entry = {}
            for col in row.index:
                if pd.notna(row[col]):
                    entry[col] = str(row[col])
            doctors_list.append(entry)

        return {
            "doctors": doctors_list,
            "specialty": specialty,
            "matched_specialty": matched,
            "count": len(doctors_list),
        }

    except Exception as e:
        logger.error(f"Error suggesting doctors: {e}")
        return {"doctors": [], "specialty": specialty, "count": 0}

# Flask routes

@app.route("/health", methods=["GET"])
def health_check():
    """Health check endpoint."""
    return jsonify(
        {
            "status": "ok",
            "timestamp": datetime.now().isoformat(),
            "models_loaded": {
                "intent_model": intent_model is not None,
                "vectorizer": vectorizer is not None,
                "qa_model": qa_model is not None,
                "qa_vectorizer": qa_vectorizer is not None,
            },
            "data_loaded": {
                "medquad": medquad_data is not None,
                "doctors": doctor_data is not None,
                "specialties": specialties_data is not None,
                "medical_knowledge": medical_knowledge is not None,
            },
            "health_tips_categories": list(HEALTH_TIPS.keys()),
        }
    )

@app.route("/predict_intent", methods=["POST"])
def predict_intent_endpoint():
    """POST endpoint to predict intent."""
    try:
        data = request.get_json(silent=True) or {}
        if "text" not in data:
            return jsonify({"error": "Missing 'text' parameter"}), 400
        text = str(data["text"])
        result = predict_intent(text)
        return jsonify(result)
    except Exception as e:
        logger.error(f"Error in predict_intent endpoint: {e}")
        return jsonify({"error": "Internal server error"}), 500

@app.route("/health_tips", methods=["POST"])
def health_tips_endpoint():
    """POST endpoint to get health tips."""
    try:
        data = request.get_json(silent=True) or {}
        specialty = data.get("specialty")
        count_raw = data.get("count", 5)
        try:
            count = int(count_raw)
        except Exception:
            count = 5
        result = get_health_tips(specialty, count)
        return jsonify(result)
    except Exception as e:
        logger.error(f"Error in health_tips endpoint: {e}")
        return jsonify({"error": "Internal server error"}), 500

@app.route("/answer_qa", methods=["POST"])
def answer_qa_endpoint():
    """POST endpoint to answer a medical question."""
    try:
        data = request.get_json(silent=True) or {}
        if "question" not in data:
            return jsonify({"error": "Missing 'question' parameter"}), 400
        question = str(data["question"])
        result = answer_qa(question)
        return jsonify(result)
    except Exception as e:
        logger.error(f"Error in answer_qa endpoint: {e}")
        return jsonify({"error": "Internal server error"}), 500

@app.route("/suggest_doctor", methods=["POST"])
def suggest_doctor_endpoint():
    """POST endpoint to suggest doctors."""
    try:
        data = request.get_json(silent=True) or {}
        if "specialty" not in data:
            return jsonify({"error": "Missing 'specialty' parameter"}), 400
        specialty = str(data["specialty"])
        result = suggest_doctors(specialty)
        return jsonify(result)
    except Exception as e:
        logger.error(f"Error in suggest_doctor endpoint: {e}")
        return jsonify({"error": "Internal server error"}), 500

@app.route("/test", methods=["POST"])
def test_endpoint():
    """Simple POST test endpoint."""
    try:
        data = request.get_json(silent=True) or {}
        if "text" not in data:
            return jsonify({"error": "Missing 'text' parameter"}), 400
        text = str(data["text"])
        return jsonify(
            {
                "success": True,
                "message": f"Received: {text}",
                "timestamp": datetime.now().isoformat(),
            }
        )
    except Exception as e:
        logger.error(f"Error in test endpoint: {e}")
        return jsonify({"error": str(e)}), 500

@app.route("/process", methods=["POST"])
def process_text():
    """Comprehensive processing endpoint."""
    try:
        data = request.get_json(silent=True) or {}
        if "text" not in data:
            return jsonify({"error": "Missing 'text' parameter"}), 400
        text = str(data["text"])
        logger.info(f"Processing text: {text}")

        try:
            intent_result = predict_intent(text)
            logger.info(f"Intent result: {intent_result}")
        except Exception as e:
            logger.error(f"Error in intent prediction: {e}")
            intent_result = {
                "intent": "general_inquiry",
                "confidence": 0.5,
                "specialty_hint": None,
            }

        health_tips_result = None
        try:
            if intent_result.get("intent") == "health_tips":
                spec = intent_result.get("specialty_hint")
                health_tips_result = get_health_tips(spec, 5)
        except Exception as e:
            logger.error(f"Error getting health tips: {e}")

        qa_result = None
        try:
            if "?" in text or any(
                word in text.lower()
                for word in ["what", "how", "why", "when", "where", "symptoms", "treatment"]
            ):
                qa_result = answer_qa(text)
        except Exception as e:
            logger.error(f"Error in Q&A: {e}")

        doctors_result = None
        try:
            spec_hint = intent_result.get("specialty_hint")
            if intent_result.get("intent") == "book_appointment":
                if isinstance(spec_hint, str) and spec_hint:
                    # Try to find doctors for the specific specialty
                    doctors_result = suggest_doctors(spec_hint)
                else:
                    # If no specialty specified, suggest doctors from all specialties
                    doctors_result = suggest_doctors("all")
        except Exception as e:
            logger.error(f"Error suggesting doctors: {e}")

        return jsonify(
            {
                "input_text": text,
                "intent": intent_result,
                "health_tips": health_tips_result,
                "qa": qa_result,
                "doctors": doctors_result,
            }
        )

    except Exception as e:
        logger.error(f"Error in process endpoint: {e}")
        return jsonify({"error": f"Internal server error: {str(e)}"}), 500

@app.route("/simple", methods=["GET"])
def simple_test():
    """Simple GET test endpoint."""
    return jsonify(
        {
            "status": "ok",
            "message": "Flask server is working",
            "timestamp": datetime.now().isoformat(),
        }
    )

# Main entry point
if __name__ == "__main__":
    Path("logs").mkdir(exist_ok=True)
    logger.info("🚀 Starting Enhanced Medical Voice Assistant NLP Service…")
    if load_models():
        logger.info("✅ Models loaded successfully")
    else:
        logger.warning("⚠️ Some models failed to load")
    if load_data():
        logger.info("✅ Data loaded successfully")
    else:
        logger.warning("⚠️ Some data failed to load")
    logger.info(f"✅ Health tips categories loaded: {list(HEALTH_TIPS.keys())}")
    logger.info("🌐 Starting Flask server on port 5006…")
    app.run(host="127.0.0.1", port=5006, debug=False)
