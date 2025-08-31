#!/usr/bin/env python3
"""
Medical Assistant Model Training Script
=======================================

This script trains machine learning models for:
1. Intent Classification - Classifying user queries into different intents
2. Medical Q&A - Question-answer matching for medical queries

The script includes data preprocessing, feature extraction, model training,
evaluation, and model persistence.
"""

import pandas as pd
import numpy as np
import joblib
import json
import logging
from pathlib import Path
from datetime import datetime
from typing import Dict, List, Tuple, Any
from sklearn.model_selection import train_test_split, GridSearchCV
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.ensemble import RandomForestClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.svm import SVC
from sklearn.naive_bayes import MultinomialNB
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score
from sklearn.pipeline import Pipeline
import re
import warnings
warnings.filterwarnings('ignore')

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('logs/training.log', encoding='utf-8'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

class MedicalModelTrainer:
    """Trainer class for medical assistant models."""
    
    def __init__(self, data_dir: str = 'data', models_dir: str = 'models'):
        self.data_dir = Path(data_dir)
        self.models_dir = Path(models_dir)
        self.models_dir.mkdir(exist_ok=True)
        
        # Intent labels mapping
        self.intent_labels = {
            0: 'book_appointment',
            1: 'search_doctors', 
            2: 'check_availability',
            3: 'emergency_guidance',
            4: 'health_tips',
            5: 'manage_appointments',
            6: 'symptom_inquiry',
            7: 'general_inquiry'
        }
        
        # Model configurations
        self.model_configs = {
            'intent_classification': {
                'vectorizer': {
                    'max_features': 5000,
                    'ngram_range': (1, 2),
                    'min_df': 2,
                    'max_df': 0.95,
                    'stop_words': 'english'
                },
                'classifier': {
                    'model_type': 'random_forest',
                    'params': {
                        'n_estimators': 100,
                        'max_depth': 10,
                        'random_state': 42
                    }
                }
            },
            'medical_qa': {
                'vectorizer': {
                    'max_features': 3000,
                    'ngram_range': (1, 3),
                    'min_df': 1,
                    'max_df': 0.9,
                    'stop_words': 'english'
                },
                'classifier': {
                    'model_type': 'logistic_regression',
                    'params': {
                        'C': 1.0,
                        'max_iter': 1000,
                        'random_state': 42
                    }
                }
            }
        }
    
    def create_training_data(self) -> Tuple[pd.DataFrame, pd.DataFrame]:
        """Create synthetic training data for intent classification and Q&A."""
        
        # Intent classification training data
        intent_data = []
        
        # Book appointment examples
        appointment_queries = [
            "I need to book an appointment with a cardiologist",
            "Can I schedule a visit with Dr. Smith?",
            "I want to make an appointment for tomorrow",
            "Book me a consultation with a dermatologist",
            "I need to see a doctor next week",
            "Schedule an appointment with a pediatrician",
            "Make an appointment for my annual checkup",
            "I'd like to book a session with a psychiatrist",
            "Can you arrange a visit with an orthopedist?",
            "I need an appointment with a neurologist",
            "Book me in for a dental checkup",
            "Schedule a consultation with an ophthalmologist",
            "I want to make an appointment for my child",
            "Book an appointment with a gynecologist",
            "I need to see a specialist for my back pain"
        ]
        for query in appointment_queries:
            intent_data.append({'text': query, 'intent': 0})
        
        # Search doctors examples
        doctor_queries = [
            "Find me a cardiologist",
            "I'm looking for a dermatologist",
            "Can you help me find a good pediatrician?",
            "I need to find a neurologist in my area",
            "Show me available psychiatrists",
            "Find a dentist near me",
            "I'm looking for an orthopedist",
            "Can you recommend an ophthalmologist?",
            "Find me a gynecologist",
            "I need a good internal medicine doctor",
            "Show me all cardiologists",
            "Find a specialist for heart problems",
            "I'm looking for a skin doctor",
            "Can you find me a brain specialist?",
            "I need a doctor for my child"
        ]
        for query in doctor_queries:
            intent_data.append({'text': query, 'intent': 1})
        
        # Check availability examples
        availability_queries = [
            "What appointments are available tomorrow?",
            "Check availability for next week",
            "When is Dr. Johnson available?",
            "What times are free this week?",
            "Show me available slots for today",
            "Check if there are any openings",
            "What's the schedule like for this month?",
            "Are there any appointments available?",
            "When can I book an appointment?",
            "Check availability for cardiology",
            "What times are available for dermatology?",
            "Show me free slots for pediatrics",
            "Check if Dr. Smith has any openings",
            "What's available for next month?",
            "Are there any emergency slots?"
        ]
        for query in availability_queries:
            intent_data.append({'text': query, 'intent': 2})
        
        # Emergency guidance examples
        emergency_queries = [
            "I have chest pain",
            "This is an emergency",
            "I'm having trouble breathing",
            "I need immediate medical attention",
            "I'm experiencing severe symptoms",
            "This is urgent",
            "I have a medical emergency",
            "I'm bleeding heavily",
            "I think I'm having a heart attack",
            "I need emergency care",
            "I'm unconscious",
            "I have severe abdominal pain",
            "I'm having a stroke",
            "I need an ambulance",
            "This is critical"
        ]
        for query in emergency_queries:
            intent_data.append({'text': query, 'intent': 3})
        
        # Health tips examples
        tips_queries = [
            "Give me some health tips",
            "How can I stay healthy?",
            "What are some wellness tips?",
            "I need health advice",
            "How can I improve my health?",
            "Give me nutrition tips",
            "What should I do to stay fit?",
            "I need lifestyle advice",
            "How can I prevent illness?",
            "Give me wellness guidance",
            "What are healthy habits?",
            "How can I boost my immune system?",
            "I need exercise advice",
            "What's good for my health?",
            "Give me preventive health tips"
        ]
        for query in tips_queries:
            intent_data.append({'text': query, 'intent': 4})
        
        # Manage appointments examples
        manage_queries = [
            "Show me my appointments",
            "I need to cancel my appointment",
            "Reschedule my visit",
            "View my upcoming appointments",
            "Change my appointment time",
            "I want to cancel my booking",
            "Show me my schedule",
            "I need to modify my appointment",
            "View my medical appointments",
            "Cancel my consultation",
            "I want to reschedule",
            "Show me my bookings",
            "I need to change my appointment",
            "View my medical schedule",
            "I want to cancel my session"
        ]
        for query in manage_queries:
            intent_data.append({'text': query, 'intent': 5})
        
        # Symptom inquiry examples
        symptom_queries = [
            "I have a headache",
            "I'm experiencing fever",
            "I have a cough",
            "I feel nauseous",
            "I'm dizzy",
            "I have back pain",
            "I'm experiencing fatigue",
            "I have stomach pain",
            "I'm having trouble sleeping",
            "I feel anxious",
            "I have joint pain",
            "I'm experiencing shortness of breath",
            "I have skin problems",
            "I'm feeling depressed",
            "I have vision problems"
        ]
        for query in symptom_queries:
            intent_data.append({'text': query, 'intent': 6})
        
        # General inquiry examples
        general_queries = [
            "Hello",
            "Hi there",
            "Good morning",
            "How are you?",
            "What can you help me with?",
            "I need help",
            "Can you assist me?",
            "What services do you offer?",
            "Thank you",
            "Goodbye",
            "See you later",
            "Thanks for your help",
            "I appreciate it",
            "Have a good day",
            "Take care"
        ]
        for query in general_queries:
            intent_data.append({'text': query, 'intent': 7})
        
        intent_df = pd.DataFrame(intent_data)
        
        # Medical Q&A training data
        qa_data = []
        
        # Common medical questions and answers
        qa_pairs = [
            ("What are the symptoms of diabetes?", "Common symptoms include increased thirst, frequent urination, extreme hunger, unexplained weight loss, fatigue, and blurred vision."),
            ("How can I lower my blood pressure?", "Lifestyle changes include reducing salt intake, exercising regularly, maintaining a healthy weight, limiting alcohol, and managing stress."),
            ("What causes headaches?", "Headaches can be caused by stress, dehydration, lack of sleep, eye strain, sinus problems, or underlying medical conditions."),
            ("How much sleep do I need?", "Adults typically need 7-9 hours of sleep per night, while children and teenagers need more."),
            ("What are the benefits of exercise?", "Exercise improves cardiovascular health, strengthens muscles, boosts mood, helps with weight management, and reduces disease risk."),
            ("How can I improve my diet?", "Eat more fruits and vegetables, choose whole grains, include lean protein, limit processed foods, and stay hydrated."),
            ("What are the signs of depression?", "Symptoms include persistent sadness, loss of interest, changes in appetite or sleep, fatigue, and thoughts of self-harm."),
            ("How can I manage stress?", "Try meditation, deep breathing, exercise, adequate sleep, time management, and seeking support from friends or professionals."),
            ("What causes high cholesterol?", "Factors include diet high in saturated fats, lack of exercise, obesity, smoking, and genetic predisposition."),
            ("How can I prevent heart disease?", "Maintain a healthy diet, exercise regularly, avoid smoking, manage stress, and get regular check-ups."),
            ("What are the symptoms of anxiety?", "Symptoms include excessive worry, restlessness, rapid heartbeat, sweating, trembling, and difficulty concentrating."),
            ("How can I boost my immune system?", "Get adequate sleep, eat a balanced diet, exercise regularly, manage stress, and practice good hygiene."),
            ("What causes back pain?", "Common causes include poor posture, muscle strain, injury, obesity, and underlying medical conditions."),
            ("How can I improve my mental health?", "Practice self-care, maintain social connections, exercise regularly, get adequate sleep, and seek professional help when needed."),
            ("What are the benefits of meditation?", "Meditation can reduce stress, improve focus, enhance emotional well-being, lower blood pressure, and promote better sleep.")
        ]
        
        for question, answer in qa_pairs:
            qa_data.append({
                'question': question,
                'answer': answer,
                'category': 'general_health'
            })
        
        qa_df = pd.DataFrame(qa_data)
        
        return intent_df, qa_df
    
    def preprocess_text(self, text: str) -> str:
        """Preprocess text for training."""
        # Convert to lowercase
        text = text.lower()
        
        # Remove special characters but keep spaces
        text = re.sub(r'[^a-zA-Z0-9\s]', ' ', text)
        
        # Remove extra whitespace
        text = re.sub(r'\s+', ' ', text).strip()
        
        return text
    
    def train_intent_classifier(self, data: pd.DataFrame) -> Tuple[Any, Any]:
        """Train intent classification model."""
        logger.info("Training intent classification model...")
        
        # Preprocess data
        data['processed_text'] = data['text'].apply(self.preprocess_text)
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            data['processed_text'], 
            data['intent'], 
            test_size=0.2, 
            random_state=42,
            stratify=data['intent']
        )
        
        # Create pipeline
        vectorizer = TfidfVectorizer(**self.model_configs['intent_classification']['vectorizer'])
        
        # Choose classifier based on config
        classifier_type = self.model_configs['intent_classification']['classifier']['model_type']
        classifier_params = self.model_configs['intent_classification']['classifier']['params']
        
        if classifier_type == 'random_forest':
            classifier = RandomForestClassifier(**classifier_params)
        elif classifier_type == 'logistic_regression':
            classifier = LogisticRegression(**classifier_params)
        elif classifier_type == 'svm':
            classifier = SVC(**classifier_params)
        elif classifier_type == 'naive_bayes':
            classifier = MultinomialNB()
        else:
            classifier = RandomForestClassifier(**classifier_params)
        
        # Train model
        vectorizer.fit(X_train)
        X_train_vectorized = vectorizer.transform(X_train)
        classifier.fit(X_train_vectorized, y_train)
        
        # Evaluate model
        X_test_vectorized = vectorizer.transform(X_test)
        y_pred = classifier.predict(X_test_vectorized)
        
        accuracy = accuracy_score(y_test, y_pred)
        logger.info(f"Intent classification accuracy: {accuracy:.4f}")
        
        # Print detailed metrics
        logger.info("\nClassification Report:")
        logger.info(classification_report(y_test, y_pred, target_names=list(self.intent_labels.values())))
        
        return vectorizer, classifier
    
    def train_qa_model(self, data: pd.DataFrame) -> Tuple[Any, Any]:
        """Train medical Q&A model."""
        logger.info("Training medical Q&A model...")
        
        # Preprocess questions
        data['processed_question'] = data['question'].apply(self.preprocess_text)
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            data['processed_question'],
            data['answer'],
            test_size=0.2,
            random_state=42
        )
        
        # Create pipeline
        vectorizer = TfidfVectorizer(**self.model_configs['medical_qa']['vectorizer'])
        
        # Choose classifier
        classifier_type = self.model_configs['medical_qa']['classifier']['model_type']
        classifier_params = self.model_configs['medical_qa']['classifier']['params']
        
        if classifier_type == 'logistic_regression':
            classifier = LogisticRegression(**classifier_params)
        elif classifier_type == 'random_forest':
            classifier = RandomForestClassifier(**classifier_params)
        elif classifier_type == 'svic':
            classifier = SVC(**classifier_params)
        else:
            classifier = LogisticRegression(**classifier_params)
        
        # Train model
        vectorizer.fit(X_train)
        X_train_vectorized = vectorizer.transform(X_train)
        classifier.fit(X_train_vectorized, y_train)
        
        # Evaluate model
        X_test_vectorized = vectorizer.transform(X_test)
        y_pred = classifier.predict(X_test_vectorized)
        
        # For Q&A, we'll use a simple accuracy based on exact matches
        exact_matches = sum(1 for pred, true in zip(y_pred, y_test) if pred == true)
        accuracy = exact_matches / len(y_test)
        logger.info(f"Medical Q&A accuracy: {accuracy:.4f}")
        
        return vectorizer, classifier
    
    def save_models(self, intent_vectorizer: Any, intent_classifier: Any, 
                   qa_vectorizer: Any, qa_classifier: Any) -> None:
        """Save trained models to disk."""
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        
        # Save intent classification models
        intent_vectorizer_path = self.models_dir / f'vectorizer_{timestamp}.joblib'
        intent_classifier_path = self.models_dir / f'intent_model_{timestamp}.joblib'
        
        joblib.dump(intent_vectorizer, intent_vectorizer_path)
        joblib.dump(intent_classifier, intent_classifier_path)
        
        logger.info(f"Saved intent vectorizer to: {intent_vectorizer_path}")
        logger.info(f"Saved intent classifier to: {intent_classifier_path}")
        
        # Save Q&A models
        qa_vectorizer_path = self.models_dir / f'medical_qa_vectorizer_{timestamp}.joblib'
        qa_classifier_path = self.models_dir / f'medical_qa_model_{timestamp}.joblib'
        
        joblib.dump(qa_vectorizer, qa_vectorizer_path)
        joblib.dump(qa_classifier, qa_classifier_path)
        
        logger.info(f"Saved Q&A vectorizer to: {qa_vectorizer_path}")
        logger.info(f"Saved Q&A classifier to: {qa_classifier_path}")
        
        # Save model metadata
        metadata = {
            'timestamp': timestamp,
            'intent_labels': self.intent_labels,
            'model_configs': self.model_configs,
            'training_info': {
                'intent_vectorizer_path': str(intent_vectorizer_path),
                'intent_classifier_path': str(intent_classifier_path),
                'qa_vectorizer_path': str(qa_vectorizer_path),
                'qa_classifier_path': str(qa_classifier_path)
            }
        }
        
        metadata_path = self.models_dir / f'model_metadata_{timestamp}.json'
        with open(metadata_path, 'w') as f:
            json.dump(metadata, f, indent=2)
        
        logger.info(f"Saved model metadata to: {metadata_path}")
    
    def train_all_models(self) -> None:
        """Train all models and save them."""
        logger.info("Starting model training process...")
        
        # Create training data
        intent_data, qa_data = self.create_training_data()
        
        logger.info(f"Created {len(intent_data)} intent classification samples")
        logger.info(f"Created {len(qa_data)} Q&A samples")
        
        # Train intent classifier
        intent_vectorizer, intent_classifier = self.train_intent_classifier(intent_data)
        
        # Train Q&A model
        qa_vectorizer, qa_classifier = self.train_qa_model(qa_data)
        
        # Save models
        self.save_models(intent_vectorizer, intent_classifier, qa_vectorizer, qa_classifier)
        
        logger.info("Model training completed successfully!")

def main():
    """Main training function."""
    # Create logs directory
    Path('logs').mkdir(exist_ok=True)
    
    # Initialize trainer
    trainer = MedicalModelTrainer()
    
    # Train all models
    trainer.train_all_models()

if __name__ == '__main__':
    main()
