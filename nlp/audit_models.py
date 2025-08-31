#!/usr/bin/env python3
"""
Model Audit and Organization Script
Detects latest models by timestamp and creates symlinks/archives
"""

import os
import json
import shutil
import glob
from datetime import datetime
from pathlib import Path

def extract_timestamp(filename):
    """Extract timestamp from filename like intent_model_20250824_144031.joblib"""
    try:
        # Look for pattern YYYYMMDD_HHMMSS
        import re
        match = re.search(r'_(\d{8}_\d{6})', filename)
        if match:
            timestamp_str = match.group(1)
            return datetime.strptime(timestamp_str, '%Y%m%d_%H%M%S')
    except:
        pass
    return datetime.min

def audit_models():
    """Audit and organize model files"""
    models_dir = Path('models')
    archive_dir = models_dir / 'archive'
    
    # Ensure archive directory exists
    archive_dir.mkdir(exist_ok=True)
    
    # Model patterns to audit
    model_patterns = {
        'intent_model.joblib': 'intent_model_*.joblib',
        'vectorizer.joblib': 'vectorizer_*.joblib',
        'medical_qa_model.joblib': 'medical_qa_model_*.joblib',
        'medical_qa_vectorizer.joblib': 'medical_qa_vectorizer_*.joblib'
    }
    
    models_index = {
        'audit_date': datetime.now().isoformat(),
        'models': {},
        'archived': []
    }
    
    for target_name, pattern in model_patterns.items():
        files = list(models_dir.glob(pattern))
        
        if not files:
            print(f"⚠️  No files found for pattern: {pattern}")
            continue
            
        # Sort by timestamp (newest first)
        files_with_timestamps = []
        for file in files:
            timestamp = extract_timestamp(file.name)
            files_with_timestamps.append((file, timestamp))
        
        files_with_timestamps.sort(key=lambda x: x[1], reverse=True)
        
        if files_with_timestamps:
            latest_file, latest_timestamp = files_with_timestamps[0]
            
            # Archive older files
            for file, timestamp in files_with_timestamps[1:]:
                if timestamp != datetime.min:  # Only archive timestamped files
                    archive_path = archive_dir / file.name
                    if not archive_path.exists():
                        shutil.move(str(file), str(archive_path))
                        models_index['archived'].append({
                            'original': str(file),
                            'archived': str(archive_path),
                            'timestamp': timestamp.isoformat()
                        })
                        print(f"📦 Archived: {file.name}")
            
            # Create symlink or copy for latest
            target_path = models_dir / target_name
            if target_path.exists() and target_path.is_symlink():
                target_path.unlink()
            elif target_path.exists():
                target_path.unlink()
            
            # On Windows, copy instead of symlink
            shutil.copy2(latest_file, target_path)
            
            models_index['models'][target_name] = {
                'source': str(latest_file),
                'timestamp': latest_timestamp.isoformat(),
                'size_bytes': latest_file.stat().st_size
            }
            
            print(f"✅ {target_name} → {latest_file.name} ({latest_timestamp.strftime('%Y-%m-%d %H:%M:%S')})")
    
    # Save models index
    with open(models_dir / 'MODELS_INDEX.json', 'w', encoding='utf-8') as f:
        json.dump(models_index, f, indent=2, ensure_ascii=False)
    
    print(f"\n📋 Models index saved to: {models_dir / 'MODELS_INDEX.json'}")
    return models_index

if __name__ == '__main__':
    print("🔍 Auditing model files...")
    audit_models()
    print("✅ Model audit complete!")
