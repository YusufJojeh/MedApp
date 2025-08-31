#!/usr/bin/env python3
"""
Medical Assistant Data Preparation Script
=========================================

This script prepares various datasets for the medical assistant:
1. MedQuAD dataset for medical Q&A
2. Doctor names and specialties dataset
3. Medical specialties mapping
4. Medical knowledge base
5. Working hours templates

The script generates synthetic but realistic medical data for training and testing.
"""

import pandas as pd
import json
import numpy as np
from pathlib import Path
from datetime import datetime, timedelta
import random
from typing import List, Dict, Any
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class MedicalDataGenerator:
    """Generate synthetic medical data for training and testing."""
    
    def __init__(self, data_dir: str = 'data'):
        self.data_dir = Path(data_dir)
        self.data_dir.mkdir(exist_ok=True)
        
        # Medical specialties
        self.specialties = [
            {'name': 'Cardiology', 'name_ar': 'أمراض القلب', 'description': 'Heart and cardiovascular system'},
            {'name': 'Dermatology', 'name_ar': 'الأمراض الجلدية', 'description': 'Skin, hair, and nails'},
            {'name': 'Neurology', 'name_ar': 'الأمراض العصبية', 'description': 'Brain and nervous system'},
            {'name': 'Orthopedics', 'name_ar': 'جراحة العظام', 'description': 'Bones, joints, and muscles'},
            {'name': 'Pediatrics', 'name_ar': 'طب الأطفال', 'description': 'Children and adolescents'},
            {'name': 'Psychiatry', 'name_ar': 'الطب النفسي', 'description': 'Mental health and behavior'},
            {'name': 'Ophthalmology', 'name_ar': 'طب العيون', 'description': 'Eyes and vision'},
            {'name': 'Dentistry', 'name_ar': 'طب الأسنان', 'description': 'Teeth and oral health'},
            {'name': 'Obstetrics & Gynecology', 'name_ar': 'طب النساء والولادة', 'description': 'Women\'s health and pregnancy'},
            {'name': 'Internal Medicine', 'name_ar': 'الطب الباطني', 'description': 'General adult medicine'},
            {'name': 'Surgery', 'name_ar': 'الجراحة العامة', 'description': 'Surgical procedures'},
            {'name': 'Emergency Medicine', 'name_ar': 'طب الطوارئ', 'description': 'Emergency care'},
            {'name': 'Radiology', 'name_ar': 'الأشعة', 'description': 'Medical imaging'},
            {'name': 'Anesthesiology', 'name_ar': 'التخدير', 'description': 'Anesthesia and pain management'},
            {'name': 'Oncology', 'name_ar': 'الأورام', 'description': 'Cancer treatment'}
        ]
        
        # Sample doctor names
        self.doctor_names = [
            'Dr. Sarah Johnson', 'Dr. Michael Chen', 'Dr. Emily Rodriguez', 'Dr. David Thompson',
            'Dr. Lisa Wang', 'Dr. James Wilson', 'Dr. Maria Garcia', 'Dr. Robert Brown',
            'Dr. Jennifer Lee', 'Dr. Christopher Davis', 'Dr. Amanda Martinez', 'Dr. Kevin Taylor',
            'Dr. Rachel Green', 'Dr. Daniel Anderson', 'Dr. Nicole White', 'Dr. Matthew Clark',
            'Dr. Jessica Hall', 'Dr. Andrew Lewis', 'Dr. Samantha Turner', 'Dr. Brian Moore',
            'Dr. Ashley Jackson', 'Dr. Steven Martin', 'Dr. Kimberly Lee', 'Dr. Jonathan Harris',
            'Dr. Michelle Young', 'Dr. Ryan King', 'Dr. Stephanie Wright', 'Dr. Joshua Scott',
            'Dr. Danielle Baker', 'Dr. Nathan Adams', 'Dr. Lauren Nelson', 'Dr. Tyler Carter',
            'Dr. Rebecca Mitchell', 'Dr. Brandon Roberts', 'Dr. Victoria Phillips', 'Dr. Sean Campbell',
            'Dr. Nicole Parker', 'Dr. Kyle Evans', 'Dr. Brittany Edwards', 'Dr. Derek Collins',
            'Dr. Megan Stewart', 'Dr. Corey Morris', 'Dr. Heather Rogers', 'Dr. Travis Reed',
            'Dr. Crystal Cook', 'Dr. Marcus Bailey', 'Dr. Tiffany Rivera', 'Dr. Gregory Cooper',
            'Dr. Jasmine Richardson', 'Dr. Raymond Cox', 'Dr. Monique Ward', 'Dr. Trevor Torres'
        ]
        
        # Languages
        self.languages = [
            'English', 'Arabic', 'Spanish', 'French', 'German', 'Italian', 'Portuguese',
            'Russian', 'Chinese', 'Japanese', 'Korean', 'Hindi', 'Turkish', 'Dutch'
        ]
        
        # Education institutions
        self.education_institutions = [
            'Harvard Medical School', 'Johns Hopkins University', 'Stanford University',
            'Mayo Clinic School of Medicine', 'UCLA School of Medicine', 'Yale School of Medicine',
            'Columbia University', 'University of Pennsylvania', 'Duke University',
            'University of Michigan', 'University of California San Francisco',
            'Washington University in St. Louis', 'Vanderbilt University', 'Cornell University',
            'Northwestern University', 'University of Chicago', 'Emory University',
            'University of Washington', 'University of Pittsburgh', 'University of Virginia'
        ]
    
    def generate_medquad_dataset(self) -> pd.DataFrame:
        """Generate MedQuAD-style medical Q&A dataset."""
        logger.info("Generating MedQuAD dataset...")
        
        qa_pairs = [
            # Cardiology
            ("What are the symptoms of a heart attack?", "Common symptoms include chest pain or discomfort, shortness of breath, nausea, lightheadedness, and pain in the arms, neck, jaw, or back."),
            ("How can I prevent heart disease?", "Prevent heart disease by maintaining a healthy diet, exercising regularly, avoiding smoking, managing stress, and getting regular check-ups."),
            ("What is high blood pressure?", "High blood pressure (hypertension) is when the force of blood against artery walls is consistently too high, which can lead to heart disease and stroke."),
            ("What causes chest pain?", "Chest pain can be caused by heart problems, lung issues, digestive problems, muscle strain, anxiety, or other conditions."),
            ("How is heart disease diagnosed?", "Heart disease is diagnosed through physical exams, blood tests, electrocardiograms, echocardiograms, stress tests, and other imaging procedures."),
            
            # Dermatology
            ("What causes acne?", "Acne is caused by clogged hair follicles, excess oil production, bacteria, and inflammation. Hormonal changes, stress, and certain medications can trigger it."),
            ("How can I prevent skin cancer?", "Prevent skin cancer by using sunscreen, avoiding excessive sun exposure, wearing protective clothing, and getting regular skin checks."),
            ("What is eczema?", "Eczema is a skin condition that causes red, itchy, and inflamed patches. It's often related to allergies and can be managed with proper skincare."),
            ("How do I treat a sunburn?", "Treat sunburn by cooling the skin, staying hydrated, using aloe vera, taking pain relievers, and avoiding further sun exposure."),
            ("What causes hives?", "Hives are caused by allergic reactions, stress, infections, or physical triggers. They appear as red, itchy welts on the skin."),
            
            # Neurology
            ("What causes migraines?", "Migraines can be caused by genetic factors, hormonal changes, stress, certain foods, environmental factors, and sleep disturbances."),
            ("How is epilepsy diagnosed?", "Epilepsy is diagnosed through medical history, neurological exams, EEG tests, brain imaging, and blood tests."),
            ("What are the symptoms of a stroke?", "Stroke symptoms include sudden numbness, confusion, trouble speaking, vision problems, dizziness, and severe headache."),
            ("What causes memory loss?", "Memory loss can be caused by aging, stress, sleep deprivation, medications, alcohol, and neurological conditions."),
            ("How can I improve brain health?", "Improve brain health through regular exercise, healthy diet, adequate sleep, mental stimulation, and stress management."),
            
            # Orthopedics
            ("What causes back pain?", "Back pain can be caused by muscle strain, poor posture, injury, obesity, stress, and underlying medical conditions."),
            ("How do I treat a sprained ankle?", "Treat a sprained ankle with rest, ice, compression, elevation (RICE), pain medication, and physical therapy."),
            ("What is arthritis?", "Arthritis is inflammation of joints causing pain, stiffness, and reduced mobility. There are many types including osteoarthritis and rheumatoid arthritis."),
            ("How can I prevent osteoporosis?", "Prevent osteoporosis with calcium-rich diet, vitamin D, regular exercise, avoiding smoking, and limiting alcohol."),
            ("What causes joint pain?", "Joint pain can be caused by injury, arthritis, overuse, infection, autoimmune diseases, and other medical conditions."),
            
            # Pediatrics
            ("When should my child get vaccinated?", "Children should follow the recommended vaccination schedule from birth through adolescence to protect against serious diseases."),
            ("How much sleep do children need?", "Children need 9-12 hours of sleep depending on age. Newborns need 14-17 hours, while teenagers need 8-10 hours."),
            ("What are common childhood illnesses?", "Common childhood illnesses include colds, flu, ear infections, strep throat, chickenpox, and stomach viruses."),
            ("How can I help my child's development?", "Support child development through reading, play, healthy nutrition, regular check-ups, and positive reinforcement."),
            ("When should I take my child to the doctor?", "Take your child to the doctor for fever, persistent symptoms, unusual behavior, or if you're concerned about their health."),
            
            # Psychiatry
            ("What are the symptoms of depression?", "Depression symptoms include persistent sadness, loss of interest, changes in appetite or sleep, fatigue, and thoughts of self-harm."),
            ("How can I manage anxiety?", "Manage anxiety through therapy, medication, relaxation techniques, exercise, adequate sleep, and stress management."),
            ("What causes insomnia?", "Insomnia can be caused by stress, anxiety, depression, medications, caffeine, alcohol, and underlying medical conditions."),
            ("How do I know if I need therapy?", "Consider therapy if you're struggling with emotions, relationships, stress, trauma, or if daily life is significantly affected."),
            ("What is bipolar disorder?", "Bipolar disorder is a mental health condition characterized by extreme mood swings including emotional highs and lows."),
            
            # Ophthalmology
            ("What causes vision problems?", "Vision problems can be caused by aging, genetics, injury, disease, and environmental factors like excessive screen time."),
            ("How can I protect my eyes?", "Protect your eyes by wearing sunglasses, taking screen breaks, eating eye-healthy foods, and getting regular eye exams."),
            ("What is glaucoma?", "Glaucoma is a group of eye conditions that damage the optic nerve, often caused by high eye pressure and leading to vision loss."),
            ("How do I know if I need glasses?", "You may need glasses if you experience blurry vision, headaches, eye strain, or difficulty seeing at distance or up close."),
            ("What causes dry eyes?", "Dry eyes can be caused by aging, medications, environmental factors, screen time, and underlying medical conditions."),
            
            # General Health
            ("How can I boost my immune system?", "Boost your immune system with adequate sleep, healthy diet, regular exercise, stress management, and good hygiene."),
            ("What are the benefits of exercise?", "Exercise improves cardiovascular health, strengthens muscles, boosts mood, helps with weight management, and reduces disease risk."),
            ("How much water should I drink?", "Most adults should drink 8-10 glasses of water daily, but needs vary based on activity level, climate, and health conditions."),
            ("What causes fatigue?", "Fatigue can be caused by lack of sleep, stress, poor nutrition, medical conditions, medications, and lifestyle factors."),
            ("How can I improve my sleep?", "Improve sleep by maintaining a regular schedule, creating a comfortable environment, avoiding screens before bed, and managing stress.")
        ]
        
        data = []
        for question, answer in qa_pairs:
            data.append({
                'question': question,
                'answer': answer,
                'category': 'medical_qa',
                'source': 'synthetic'
            })
        
        df = pd.DataFrame(data)
        return df
    
    def generate_doctor_dataset(self) -> pd.DataFrame:
        """Generate doctor names and specialties dataset."""
        logger.info("Generating doctor dataset...")
        
        data = []
        for i, name in enumerate(self.doctor_names):
            specialty = random.choice(self.specialties)
            experience = random.randint(1, 30)
            consultation_fee = random.randint(50, 300)
            education = random.choice(self.education_institutions)
            languages = random.sample(self.languages, random.randint(1, 3))
            
            data.append({
                'id': i + 1,
                'name': name,
                'specialty': specialty['name'],
                'specialty_ar': specialty['name_ar'],
                'experience_years': experience,
                'consultation_fee': consultation_fee,
                'education': education,
                'languages': ', '.join(languages),
                'is_active': 1,
                'is_featured': random.choice([0, 1])
            })
        
        df = pd.DataFrame(data)
        return df
    
    def generate_specialties_dataset(self) -> pd.DataFrame:
        """Generate medical specialties dataset."""
        logger.info("Generating specialties dataset...")
        
        data = []
        for i, specialty in enumerate(self.specialties):
            data.append({
                'id': i + 1,
                'name': specialty['name'],
                'name_ar': specialty['name_ar'],
                'name_en': specialty['name'],
                'description': specialty['description'],
                'is_active': 1
            })
        
        df = pd.DataFrame(data)
        return df
    
    def generate_medical_knowledge(self) -> Dict[str, Any]:
        """Generate medical knowledge base."""
        logger.info("Generating medical knowledge base...")
        
        knowledge = {
            'medical_conditions': {
                'diabetes': {
                    'symptoms': ['increased thirst', 'frequent urination', 'extreme hunger', 'unexplained weight loss', 'fatigue'],
                    'specialist': 'endocrinologist',
                    'description': 'A chronic condition affecting how your body metabolizes glucose'
                },
                'hypertension': {
                    'symptoms': ['headaches', 'shortness of breath', 'nosebleeds', 'chest pain', 'dizziness'],
                    'specialist': 'cardiologist',
                    'description': 'High blood pressure that can lead to serious health problems'
                },
                'asthma': {
                    'symptoms': ['wheezing', 'shortness of breath', 'chest tightness', 'coughing', 'rapid breathing'],
                    'specialist': 'pulmonologist',
                    'description': 'A condition that affects the airways in the lungs'
                },
                'depression': {
                    'symptoms': ['persistent sadness', 'loss of interest', 'changes in appetite', 'sleep problems', 'fatigue'],
                    'specialist': 'psychiatrist',
                    'description': 'A mental health disorder characterized by persistently depressed mood'
                },
                'arthritis': {
                    'symptoms': ['joint pain', 'stiffness', 'swelling', 'reduced range of motion', 'fatigue'],
                    'specialist': 'rheumatologist',
                    'description': 'Inflammation of joints causing pain and stiffness'
                }
            },
            'medications': {
                'aspirin': {
                    'uses': ['pain relief', 'fever reduction', 'blood thinning'],
                    'side_effects': ['stomach upset', 'bleeding risk', 'allergic reactions'],
                    'category': 'NSAID'
                },
                'ibuprofen': {
                    'uses': ['pain relief', 'inflammation reduction', 'fever reduction'],
                    'side_effects': ['stomach irritation', 'kidney problems', 'allergic reactions'],
                    'category': 'NSAID'
                },
                'acetaminophen': {
                    'uses': ['pain relief', 'fever reduction'],
                    'side_effects': ['liver damage', 'allergic reactions'],
                    'category': 'analgesic'
                }
            },
            'procedures': {
                'blood_test': {
                    'description': 'Laboratory analysis of blood samples',
                    'preparation': 'May require fasting',
                    'duration': '5-10 minutes',
                    'specialist': 'laboratory technician'
                },
                'x_ray': {
                    'description': 'Imaging test using radiation',
                    'preparation': 'Remove metal objects',
                    'duration': '15-30 minutes',
                    'specialist': 'radiologist'
                },
                'mri': {
                    'description': 'Magnetic resonance imaging',
                    'preparation': 'Remove all metal objects',
                    'duration': '30-60 minutes',
                    'specialist': 'radiologist'
                }
            }
        }
        
        return knowledge
    
    def generate_working_hours_template(self) -> pd.DataFrame:
        """Generate working hours template for doctors."""
        logger.info("Generating working hours template...")
        
        data = []
        days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
        
        for day in days:
            if day in ['Saturday', 'Sunday']:
                # Weekend hours (shorter)
                data.append({
                    'day_of_week': days.index(day),
                    'day_name': day,
                    'start_time': '09:00:00',
                    'end_time': '17:00:00',
                    'is_available': 1,
                    'break_start': '12:00:00',
                    'break_end': '13:00:00'
                })
            else:
                # Weekday hours (full day)
                data.append({
                    'day_of_week': days.index(day),
                    'day_name': day,
                    'start_time': '08:00:00',
                    'end_time': '18:00:00',
                    'is_available': 1,
                    'break_start': '12:00:00',
                    'break_end': '13:00:00'
                })
        
        df = pd.DataFrame(data)
        return df
    
    def save_datasets(self) -> None:
        """Generate and save all datasets."""
        logger.info("Starting data generation...")
        
        # Generate datasets
        medquad_df = self.generate_medquad_dataset()
        doctor_df = self.generate_doctor_dataset()
        specialties_df = self.generate_specialties_dataset()
        medical_knowledge = self.generate_medical_knowledge()
        working_hours_df = self.generate_working_hours_template()
        
        # Save to files
        medquad_df.to_csv(self.data_dir / 'medquad.csv', index=False)
        doctor_df.to_csv(self.data_dir / 'doctor_names.csv', index=False)
        specialties_df.to_csv(self.data_dir / 'specialties_ar_en.csv', index=False)
        working_hours_df.to_csv(self.data_dir / 'working_hours_template.csv', index=False)
        
        with open(self.data_dir / 'medical_knowledge.json', 'w', encoding='utf-8') as f:
            json.dump(medical_knowledge, f, indent=2, ensure_ascii=False)
        
        # Print summary
        logger.info(f"Generated {len(medquad_df)} Q&A pairs")
        logger.info(f"Generated {len(doctor_df)} doctor records")
        logger.info(f"Generated {len(specialties_df)} specialty records")
        logger.info(f"Generated {len(working_hours_df)} working hours templates")
        logger.info("All datasets saved successfully!")

def main():
    """Main function to generate all datasets."""
    generator = MedicalDataGenerator()
    generator.save_datasets()

if __name__ == '__main__':
    main()
