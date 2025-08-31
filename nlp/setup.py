#!/usr/bin/env python3
"""
Setup script for Medical Assistant NLP System
=============================================

This script installs the medical assistant NLP system as a Python package.
"""

from setuptools import setup, find_packages
from pathlib import Path

# Read the README file
this_directory = Path(__file__).parent
long_description = (this_directory / "README.md").read_text(encoding='utf-8')

# Read requirements
requirements = []
with open('requirements.txt', 'r', encoding='utf-8') as f:
    for line in f:
        line = line.strip()
        if line and not line.startswith('#'):
            requirements.append(line)

setup(
    name="medical-assistant-nlp",
    version="1.0.0",
    author="Medical Assistant Team",
    author_email="support@medicalassistant.com",
    description="AI-powered medical assistant with NLP capabilities",
    long_description=long_description,
    long_description_content_type="text/markdown",
    url="https://github.com/medical-assistant/nlp-system",
    packages=find_packages(),
    classifiers=[
        "Development Status :: 4 - Beta",
        "Intended Audience :: Healthcare Industry",
        "License :: OSI Approved :: MIT License",
        "Operating System :: OS Independent",
        "Programming Language :: Python :: 3",
        "Programming Language :: Python :: 3.8",
        "Programming Language :: Python :: 3.9",
        "Programming Language :: Python :: 3.10",
        "Programming Language :: Python :: 3.11",
        "Topic :: Scientific/Engineering :: Artificial Intelligence",
        "Topic :: Software Development :: Libraries :: Python Modules",
    ],
    python_requires=">=3.8",
    install_requires=requirements,
    extras_require={
        "dev": [
            "pytest>=7.4.0",
            "pytest-cov>=4.1.0",
            "black>=23.0.0",
            "flake8>=6.0.0",
            "mypy>=1.5.0",
        ],
        "advanced": [
            "transformers>=4.30.0",
            "torch>=2.0.0",
            "sentence-transformers>=2.2.0",
        ],
        "api": [
            "flask-restx>=1.1.0",
            "flask-cors>=4.0.0",
        ],
    },
    entry_points={
        "console_scripts": [
            "medical-assistant-train=train_models:main",
            "medical-assistant-evaluate=model_evaluation:main",
            "medical-assistant-prepare-data=data_preparation:main",
            "medical-assistant-server=server:main",
        ],
    },
    include_package_data=True,
    package_data={
        "": ["*.json", "*.csv", "*.joblib", "*.md"],
    },
    keywords="medical, healthcare, nlp, ai, machine learning, chatbot",
    project_urls={
        "Bug Reports": "https://github.com/medical-assistant/nlp-system/issues",
        "Source": "https://github.com/medical-assistant/nlp-system",
        "Documentation": "https://medical-assistant.readthedocs.io/",
    },
)
