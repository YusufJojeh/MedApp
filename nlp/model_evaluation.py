#!/usr/bin/env python3
"""
Medical Assistant Model Evaluation Script
=========================================

This script evaluates the trained models for:
1. Intent Classification accuracy and performance
2. Medical Q&A model performance
3. Model robustness and generalization

Includes cross-validation, confusion matrix analysis, and performance metrics.
"""

import pandas as pd
import numpy as np
import joblib
import json
import logging
from pathlib import Path
from datetime import datetime
from typing import Dict, List, Tuple, Any
from sklearn.model_selection import cross_val_score, StratifiedKFold
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score
from sklearn.metrics import precision_recall_fscore_support, roc_auc_score
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.feature_extraction.text import TfidfVectorizer
import warnings
warnings.filterwarnings('ignore')

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class ModelEvaluator:
    """Evaluate trained medical assistant models."""
    
    def __init__(self, models_dir: str = 'models', data_dir: str = 'data'):
        self.models_dir = Path(models_dir)
        self.data_dir = Path(data_dir)
        
        # Intent labels
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
        
        # Load models
        self.load_models()
    
    def load_models(self) -> None:
        """Load trained models from disk."""
        try:
            # Find latest model files
            model_files = list(self.models_dir.glob('*.joblib'))
            if not model_files:
                logger.error("No model files found!")
                return
            
            # Load intent classification models
            intent_vectorizer_files = [f for f in model_files if 'vectorizer' in f.name and 'qa' not in f.name]
            intent_model_files = [f for f in model_files if 'intent_model' in f.name]
            
            if intent_vectorizer_files and intent_model_files:
                self.intent_vectorizer = joblib.load(intent_vectorizer_files[-1])
                self.intent_model = joblib.load(intent_model_files[-1])
                logger.info(f"Loaded intent models: {intent_vectorizer_files[-1].name}, {intent_model_files[-1].name}")
            
            # Load Q&A models
            qa_vectorizer_files = [f for f in model_files if 'qa_vectorizer' in f.name]
            qa_model_files = [f for f in model_files if 'qa_model' in f.name]
            
            if qa_vectorizer_files and qa_model_files:
                self.qa_vectorizer = joblib.load(qa_vectorizer_files[-1])
                self.qa_model = joblib.load(qa_model_files[-1])
                logger.info(f"Loaded Q&A models: {qa_vectorizer_files[-1].name}, {qa_model_files[-1].name}")
            
        except Exception as e:
            logger.error(f"Error loading models: {e}")
    
    def create_test_data(self) -> Tuple[pd.DataFrame, pd.DataFrame]:
        """Create test data for evaluation."""
        logger.info("Creating test data...")
        
        # Intent classification test data
        intent_test_data = [
            # Book appointment test cases
            ("I need to schedule an appointment with a cardiologist", 0),
            ("Can I book a visit with Dr. Smith for next week?", 0),
            ("I want to make an appointment for my annual checkup", 0),
            ("Schedule me with a dermatologist", 0),
            
            # Search doctors test cases
            ("Find me a good neurologist", 1),
            ("I'm looking for a pediatrician in my area", 1),
            ("Can you recommend an orthopedist?", 1),
            ("Show me available psychiatrists", 1),
            
            # Check availability test cases
            ("What appointments are available tomorrow?", 2),
            ("Check availability for next week", 2),
            ("When is Dr. Johnson available?", 2),
            ("What times are free this week?", 2),
            
            # Emergency guidance test cases
            ("I have severe chest pain", 3),
            ("This is a medical emergency", 3),
            ("I'm having trouble breathing", 3),
            ("I need immediate medical attention", 3),
            
            # Health tips test cases
            ("Give me some wellness tips", 4),
            ("How can I stay healthy?", 4),
            ("I need health advice", 4),
            ("What are some healthy habits?", 4),
            
            # Manage appointments test cases
            ("Show me my appointments", 5),
            ("I need to cancel my appointment", 5),
            ("Reschedule my visit", 5),
            ("View my upcoming appointments", 5),
            
            # Symptom inquiry test cases
            ("I have a persistent headache", 6),
            ("I'm experiencing fever", 6),
            ("I feel nauseous", 6),
            ("I have back pain", 6),
            
            # General inquiry test cases
            ("Hello, how are you?", 7),
            ("What can you help me with?", 7),
            ("Thank you for your help", 7),
            ("Goodbye", 7)
        ]
        
        intent_df = pd.DataFrame(intent_test_data, columns=['text', 'intent'])
        
        # Q&A test data
        qa_test_data = [
            ("What are the symptoms of diabetes?", "Common symptoms include increased thirst, frequent urination, extreme hunger, unexplained weight loss, fatigue, and blurred vision."),
            ("How can I lower my blood pressure?", "Lifestyle changes include reducing salt intake, exercising regularly, maintaining a healthy weight, limiting alcohol, and managing stress."),
            ("What causes headaches?", "Headaches can be caused by stress, dehydration, lack of sleep, eye strain, sinus problems, or underlying medical conditions."),
            ("How much sleep do I need?", "Adults typically need 7-9 hours of sleep per night, while children and teenagers need more."),
            ("What are the benefits of exercise?", "Exercise improves cardiovascular health, strengthens muscles, boosts mood, helps with weight management, and reduces disease risk.")
        ]
        
        qa_df = pd.DataFrame(qa_test_data, columns=['question', 'answer'])
        
        return intent_df, qa_df
    
    def preprocess_text(self, text: str) -> str:
        """Preprocess text for evaluation."""
        import re
        # Convert to lowercase
        text = text.lower()
        # Remove special characters but keep spaces
        text = re.sub(r'[^a-zA-Z0-9\s]', ' ', text)
        # Remove extra whitespace
        text = re.sub(r'\s+', ' ', text).strip()
        return text
    
    def evaluate_intent_classifier(self, test_data: pd.DataFrame) -> Dict[str, Any]:
        """Evaluate intent classification model."""
        logger.info("Evaluating intent classification model...")
        
        if not hasattr(self, 'intent_vectorizer') or not hasattr(self, 'intent_model'):
            logger.error("Intent classification models not loaded!")
            return {}
        
        # Preprocess test data
        test_data['processed_text'] = test_data['text'].apply(self.preprocess_text)
        
        # Vectorize test data
        X_test = self.intent_vectorizer.transform(test_data['processed_text'])
        y_test = test_data['intent']
        
        # Make predictions
        y_pred = self.intent_model.predict(X_test)
        y_pred_proba = self.intent_model.predict_proba(X_test)
        
        # Calculate metrics
        accuracy = accuracy_score(y_test, y_pred)
        precision, recall, f1, _ = precision_recall_fscore_support(y_test, y_pred, average='weighted')
        
        # Calculate per-class metrics
        class_precision, class_recall, class_f1, _ = precision_recall_fscore_support(y_test, y_pred, average=None)
        
        # Create confusion matrix
        cm = confusion_matrix(y_test, y_pred)
        
        # Calculate ROC AUC (one-vs-rest)
        try:
            roc_auc = roc_auc_score(y_test, y_pred_proba, multi_class='ovr')
        except:
            roc_auc = None
        
        # Cross-validation
        cv_scores = cross_val_score(
            self.intent_model, 
            X_test, 
            y_test, 
            cv=StratifiedKFold(n_splits=5, shuffle=True, random_state=42),
            scoring='accuracy'
        )
        
        results = {
            'accuracy': accuracy,
            'precision': precision,
            'recall': recall,
            'f1_score': f1,
            'roc_auc': roc_auc,
            'cv_mean': cv_scores.mean(),
            'cv_std': cv_scores.std(),
            'confusion_matrix': cm,
            'class_precision': class_precision,
            'class_recall': class_recall,
            'class_f1': class_f1,
            'predictions': y_pred,
            'true_labels': y_test,
            'prediction_probabilities': y_pred_proba
        }
        
        logger.info(f"Intent Classification Results:")
        logger.info(f"  Accuracy: {accuracy:.4f}")
        logger.info(f"  Precision: {precision:.4f}")
        logger.info(f"  Recall: {recall:.4f}")
        logger.info(f"  F1-Score: {f1:.4f}")
        logger.info(f"  ROC AUC: {roc_auc:.4f}" if roc_auc else "  ROC AUC: N/A")
        logger.info(f"  CV Mean: {cv_scores.mean():.4f} (+/- {cv_scores.std() * 2:.4f})")
        
        return results
    
    def evaluate_qa_model(self, test_data: pd.DataFrame) -> Dict[str, Any]:
        """Evaluate medical Q&A model."""
        logger.info("Evaluating medical Q&A model...")
        
        if not hasattr(self, 'qa_vectorizer') or not hasattr(self, 'qa_model'):
            logger.error("Q&A models not loaded!")
            return {}
        
        # Preprocess test data
        test_data['processed_question'] = test_data['question'].apply(self.preprocess_text)
        
        # Vectorize test data
        X_test = self.qa_vectorizer.transform(test_data['processed_question'])
        y_test = test_data['answer']
        
        # Make predictions
        y_pred = self.qa_model.predict(X_test)
        
        # Calculate exact match accuracy
        exact_matches = sum(1 for pred, true in zip(y_pred, y_test) if pred == true)
        exact_accuracy = exact_matches / len(y_test)
        
        # Calculate partial match accuracy (if answers contain similar keywords)
        partial_matches = 0
        for pred, true in zip(y_pred, y_test):
            pred_words = set(pred.lower().split())
            true_words = set(true.lower().split())
            overlap = len(pred_words.intersection(true_words))
            if overlap >= min(len(pred_words), len(true_words)) * 0.3:  # 30% overlap
                partial_matches += 1
        
        partial_accuracy = partial_matches / len(y_test)
        
        results = {
            'exact_accuracy': exact_accuracy,
            'partial_accuracy': partial_accuracy,
            'total_samples': len(y_test),
            'exact_matches': exact_matches,
            'partial_matches': partial_matches,
            'predictions': y_pred,
            'true_answers': y_test
        }
        
        logger.info(f"Medical Q&A Results:")
        logger.info(f"  Exact Match Accuracy: {exact_accuracy:.4f}")
        logger.info(f"  Partial Match Accuracy: {partial_accuracy:.4f}")
        logger.info(f"  Total Samples: {len(y_test)}")
        
        return results
    
    def plot_confusion_matrix(self, cm: np.ndarray, save_path: str = None) -> None:
        """Plot confusion matrix."""
        plt.figure(figsize=(10, 8))
        sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', 
                   xticklabels=list(self.intent_labels.values()),
                   yticklabels=list(self.intent_labels.values()))
        plt.title('Intent Classification Confusion Matrix')
        plt.xlabel('Predicted')
        plt.ylabel('Actual')
        plt.xticks(rotation=45)
        plt.yticks(rotation=0)
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
            logger.info(f"Confusion matrix saved to: {save_path}")
        
        plt.show()
    
    def plot_class_performance(self, class_precision: np.ndarray, class_recall: np.ndarray, 
                             class_f1: np.ndarray, save_path: str = None) -> None:
        """Plot per-class performance metrics."""
        classes = list(self.intent_labels.values())
        
        x = np.arange(len(classes))
        width = 0.25
        
        plt.figure(figsize=(12, 6))
        plt.bar(x - width, class_precision, width, label='Precision', alpha=0.8)
        plt.bar(x, class_recall, width, label='Recall', alpha=0.8)
        plt.bar(x + width, class_f1, width, label='F1-Score', alpha=0.8)
        
        plt.xlabel('Intent Classes')
        plt.ylabel('Score')
        plt.title('Per-Class Performance Metrics')
        plt.xticks(x, classes, rotation=45)
        plt.legend()
        plt.grid(True, alpha=0.3)
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
            logger.info(f"Class performance plot saved to: {save_path}")
        
        plt.show()
    
    def generate_evaluation_report(self, intent_results: Dict[str, Any], 
                                 qa_results: Dict[str, Any]) -> str:
        """Generate comprehensive evaluation report."""
        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        
        report = f"""
Medical Assistant Model Evaluation Report
========================================
Generated: {timestamp}

INTENT CLASSIFICATION RESULTS
----------------------------
Overall Metrics:
- Accuracy: {intent_results.get('accuracy', 0):.4f}
- Precision: {intent_results.get('precision', 0):.4f}
- Recall: {intent_results.get('recall', 0):.4f}
- F1-Score: {intent_results.get('f1_score', 0):.4f}
- ROC AUC: {intent_results.get('roc_auc', 'N/A')}
- Cross-Validation: {intent_results.get('cv_mean', 0):.4f} (+/- {intent_results.get('cv_std', 0) * 2:.4f})

Per-Class Performance:
"""
        
        if 'class_precision' in intent_results:
            for i, intent_name in self.intent_labels.items():
                report += f"- {intent_name}:\n"
                report += f"  Precision: {intent_results['class_precision'][i]:.4f}\n"
                report += f"  Recall: {intent_results['class_recall'][i]:.4f}\n"
                report += f"  F1-Score: {intent_results['class_f1'][i]:.4f}\n"
        
        report += f"""

MEDICAL Q&A RESULTS
------------------
- Exact Match Accuracy: {qa_results.get('exact_accuracy', 0):.4f}
- Partial Match Accuracy: {qa_results.get('partial_accuracy', 0):.4f}
- Total Samples: {qa_results.get('total_samples', 0)}
- Exact Matches: {qa_results.get('exact_matches', 0)}
- Partial Matches: {qa_results.get('partial_matches', 0)}

RECOMMENDATIONS
--------------
"""
        
        # Add recommendations based on results
        if intent_results.get('accuracy', 0) < 0.8:
            report += "- Intent classification accuracy is below 80%. Consider:\n"
            report += "  * Adding more training data\n"
            report += "  * Tuning hyperparameters\n"
            report += "  * Using a different model architecture\n"
        
        if qa_results.get('exact_accuracy', 0) < 0.6:
            report += "- Q&A model accuracy is low. Consider:\n"
            report += "  * Expanding the knowledge base\n"
            report += "  * Using semantic similarity instead of exact matching\n"
            report += "  * Implementing a more sophisticated retrieval system\n"
        
        if intent_results.get('cv_std', 0) > 0.1:
            report += "- High cross-validation variance indicates model instability.\n"
            report += "  Consider using more robust features or regularization.\n"
        
        return report
    
    def save_evaluation_results(self, intent_results: Dict[str, Any], 
                              qa_results: Dict[str, Any]) -> None:
        """Save evaluation results to files."""
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        
        # Create results directory
        results_dir = Path('evaluation_results')
        results_dir.mkdir(exist_ok=True)
        
        # Save numerical results
        results = {
            'timestamp': timestamp,
            'intent_classification': intent_results,
            'medical_qa': qa_results
        }
        
        results_file = results_dir / f'evaluation_results_{timestamp}.json'
        with open(results_file, 'w') as f:
            json.dump(results, f, indent=2, default=str)
        
        # Generate and save report
        report = self.generate_evaluation_report(intent_results, qa_results)
        report_file = results_dir / f'evaluation_report_{timestamp}.txt'
        with open(report_file, 'w') as f:
            f.write(report)
        
        # Save plots
        if 'confusion_matrix' in intent_results:
            cm_plot_path = results_dir / f'confusion_matrix_{timestamp}.png'
            self.plot_confusion_matrix(intent_results['confusion_matrix'], str(cm_plot_path))
        
        if 'class_precision' in intent_results:
            perf_plot_path = results_dir / f'class_performance_{timestamp}.png'
            self.plot_class_performance(
                intent_results['class_precision'],
                intent_results['class_recall'],
                intent_results['class_f1'],
                str(perf_plot_path)
            )
        
        logger.info(f"Evaluation results saved to: {results_dir}")
        logger.info(f"Report saved to: {report_file}")
    
    def run_full_evaluation(self) -> None:
        """Run complete model evaluation."""
        logger.info("Starting full model evaluation...")
        
        # Create test data
        intent_test_data, qa_test_data = self.create_test_data()
        
        # Evaluate intent classifier
        intent_results = self.evaluate_intent_classifier(intent_test_data)
        
        # Evaluate Q&A model
        qa_results = self.evaluate_qa_model(qa_test_data)
        
        # Save results
        self.save_evaluation_results(intent_results, qa_results)
        
        # Print summary
        logger.info("\n" + "="*50)
        logger.info("EVALUATION SUMMARY")
        logger.info("="*50)
        logger.info(f"Intent Classification Accuracy: {intent_results.get('accuracy', 0):.4f}")
        logger.info(f"Medical Q&A Exact Accuracy: {qa_results.get('exact_accuracy', 0):.4f}")
        logger.info("="*50)

def main():
    """Main evaluation function."""
    evaluator = ModelEvaluator()
    evaluator.run_full_evaluation()

if __name__ == '__main__':
    main()
