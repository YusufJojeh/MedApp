# Medical Assistant NLP System

An AI-powered medical assistant with advanced Natural Language Processing capabilities for medical appointment booking, health information retrieval, and intelligent conversation handling.

## Features

- **Intent Classification**: Automatically classify user queries into different intents (booking, searching, health tips, etc.)
- **Medical Q&A**: Provide accurate answers to medical questions using a comprehensive knowledge base
- **Health Tips**: Generate personalized health and wellness advice
- **Doctor Recommendations**: Suggest appropriate specialists based on symptoms and needs
- **Multi-language Support**: Support for English and Arabic
- **Real-time Processing**: Fast response times for interactive conversations

## Architecture

The system consists of several key components:

1. **Intent Classification Model**: Machine learning model to classify user intent
2. **Medical Q&A Model**: Question-answering system for medical queries
3. **Knowledge Base**: Comprehensive medical information database
4. **Flask API Server**: RESTful API for integration with web applications
5. **Data Processing Pipeline**: Text preprocessing and feature extraction

## Installation

### Prerequisites

- Python 3.8 or higher
- MySQL database
- Required Python packages (see requirements.txt)

### Quick Start

1. **Clone the repository**:
```bash
git clone https://github.com/medical-assistant/nlp-system.git
cd nlp-system
```

2. **Install dependencies**:
```bash
pip install -r requirements.txt
```

3. **Set up the database**:
```bash
# Configure your database connection in config/database.php
# Import the schema from database/schema.sql
```

4. **Generate training data**:
```bash
python data_preparation.py
```

5. **Train the models**:
```bash
python train_models.py
```

6. **Start the server**:
```bash
python server.py
```

## Usage

### API Endpoints

The system provides several REST API endpoints:

#### Intent Classification
```http
POST /predict_intent
Content-Type: application/json

{
    "text": "I need to book an appointment with a cardiologist"
}
```

#### Medical Q&A
```http
POST /answer_qa
Content-Type: application/json

{
    "question": "What are the symptoms of diabetes?"
}
```

#### Health Tips
```http
POST /health_tips
Content-Type: application/json

{
    "specialty": "cardiology",
    "count": 5
}
```

#### Doctor Recommendations
```http
POST /suggest_doctor
Content-Type: application/json

{
    "specialty": "cardiology"
}
```

#### Comprehensive Processing
```http
POST /process
Content-Type: application/json

{
    "text": "I have chest pain and need to see a doctor"
}
```

### Example Usage

```python
import requests

# Initialize the client
base_url = "http://localhost:5005"

# Intent classification
response = requests.post(f"{base_url}/predict_intent", json={
    "text": "I need to book an appointment with a cardiologist"
})
intent_result = response.json()
print(f"Intent: {intent_result['intent']}")
print(f"Confidence: {intent_result['confidence']}")

# Medical Q&A
response = requests.post(f"{base_url}/answer_qa", json={
    "question": "What are the symptoms of diabetes?"
})
qa_result = response.json()
print(f"Answer: {qa_result['answer']}")

# Health tips
response = requests.post(f"{base_url}/health_tips", json={
    "specialty": "cardiology",
    "count": 3
})
tips_result = response.json()
for tip in tips_result['tips']:
    print(f"- {tip}")
```

## Model Training

### Data Preparation

The system uses synthetic training data that can be generated using:

```bash
python data_preparation.py
```

This creates:
- `data/medquad.csv`: Medical Q&A dataset
- `data/doctor_names.csv`: Doctor information
- `data/specialties_ar_en.csv`: Medical specialties
- `data/medical_knowledge.json`: Medical knowledge base

### Training Process

Train the models using:

```bash
python train_models.py
```

This will:
1. Load and preprocess training data
2. Train intent classification model
3. Train medical Q&A model
4. Save models with timestamps
5. Generate training reports

### Model Evaluation

Evaluate model performance:

```bash
python model_evaluation.py
```

This generates:
- Accuracy metrics
- Confusion matrices
- Performance plots
- Evaluation reports

## Configuration

### Model Configuration

Models can be configured in `train_models.py`:

```python
model_configs = {
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
    }
}
```

### Server Configuration

Server settings can be modified in `server.py`:

```python
app.run(host='127.0.0.1', port=5005, debug=False)
```

## API Documentation

### Intent Classification

Classifies user queries into predefined intents:

- `book_appointment`: User wants to schedule an appointment
- `search_doctors`: User is looking for doctors
- `check_availability`: User wants to check appointment availability
- `emergency_guidance`: User needs emergency medical guidance
- `health_tips`: User wants health advice
- `manage_appointments`: User wants to manage existing appointments
- `symptom_inquiry`: User is asking about symptoms
- `general_inquiry`: General questions or greetings

### Response Format

All API endpoints return JSON responses with the following structure:

```json
{
    "success": true,
    "intent": "book_appointment",
    "confidence": 0.95,
    "entities": {
        "specialty": ["cardiology"],
        "date": ["tomorrow"],
        "urgency": ["routine"]
    },
    "response": {
        "text": "I can help you book an appointment with a cardiologist...",
        "type": "appointment_booking",
        "doctors": [...],
        "action": "show_booking_form"
    }
}
```

## Health Tips Categories

The system provides health tips for various medical specialties:

- **General**: General health and wellness advice
- **Cardiology**: Heart health and cardiovascular care
- **Dermatology**: Skin care and dermatological health
- **Neurology**: Brain health and neurological care
- **Pediatrics**: Child health and development
- **Orthopedics**: Bone and joint health
- **Psychiatry**: Mental health and well-being
- **Ophthalmology**: Eye health and vision care
- **Dentistry**: Oral health and dental care
- **Nutrition**: Diet and nutritional advice

## Development

### Project Structure

```
nlp/
├── server.py              # Main Flask server
├── train_models.py        # Model training script
├── data_preparation.py    # Data generation script
├── model_evaluation.py    # Model evaluation script
├── requirements.txt       # Python dependencies
├── setup.py              # Package setup
├── README.md             # This file
├── data/                 # Training and test data
│   ├── medquad.csv
│   ├── doctor_names.csv
│   ├── specialties_ar_en.csv
│   └── medical_knowledge.json
├── models/               # Trained models
│   ├── intent_model_*.joblib
│   ├── vectorizer_*.joblib
│   └── model_metadata_*.json
├── logs/                 # Log files
│   ├── assistant.log
│   └── training.log
└── evaluation_results/   # Evaluation outputs
    ├── evaluation_results_*.json
    ├── evaluation_report_*.txt
    └── *.png
```

### Adding New Features

1. **New Intent Types**: Add to `intent_labels` in training scripts
2. **New Health Tips**: Extend `HEALTH_TIPS` dictionary in `server.py`
3. **New Medical Knowledge**: Update `medical_knowledge.json`
4. **New API Endpoints**: Add routes in `server.py`

### Testing

Run tests using:

```bash
pytest tests/
```

### Code Quality

Format code using:

```bash
black .
flake8 .
mypy .
```

## Performance

### Model Performance

Typical performance metrics:

- **Intent Classification**: 85-95% accuracy
- **Medical Q&A**: 70-85% exact match accuracy
- **Response Time**: < 100ms for most queries

### Optimization

- Use TF-IDF vectorization for text features
- Implement caching for frequently accessed data
- Optimize database queries
- Use model compression for faster inference

## Troubleshooting

### Common Issues

1. **Model Loading Errors**: Ensure model files exist in `models/` directory
2. **Database Connection**: Check database configuration and connectivity
3. **Memory Issues**: Reduce `max_features` in vectorizer configuration
4. **Port Conflicts**: Change port in server configuration

### Logs

Check logs in `logs/` directory for detailed error information:

```bash
tail -f logs/assistant.log
tail -f logs/training.log
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:

- Create an issue on GitHub
- Contact: support@medicalassistant.com
- Documentation: https://medical-assistant.readthedocs.io/

## Acknowledgments

- Medical knowledge sources
- Open-source NLP libraries
- Healthcare professionals for domain expertise
