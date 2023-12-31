from fastapi import FastAPI
from pydantic import BaseModel
import nltk
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
import re
import pandas as pd
from scipy.sparse import hstack
import numpy as np
from joblib import load

app = FastAPI()

""" def get_data_from_db():
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="user_answer",
        port=3307 
    )

    df = pd.read_sql("SELECT * FROM modelTraining ORDER BY id  DESC LIMIT 1", conn)
    conn.close()
    return df """

df = pd.read_csv('modelTraining.csv')

@app.get("/")
def read_root():
    return {"message": "Hello World"}

# Descargar recursos necesarios de nltk
nltk.download('stopwords')
nltk.download('wordnet')

# Lematizador y stopwords
lemmatizer = WordNetLemmatizer()
stop_words = set(stopwords.words('english'))

def preprocess_text(text):
    text = text.lower()
    text = re.sub(r'[^a-z\s]', '', text)
    tokens = nltk.word_tokenize(text)
    tokens = [lemmatizer.lemmatize(token) for token in tokens if token not in stop_words]
    return ' '.join(tokens)

# Carga de modelos con joblib
encoder = load('encoder.joblib')
vectorizer = load('vectorizer.joblib')
clf = load('clf.joblib')

class RecommendationInput(BaseModel):
    interests: str
    education: str
    study_availability: str
    work_availability: str
    goals: str

@app.post("/recommend")
def recommend_course(data: RecommendationInput):
    goals_processed = preprocess_text(data.goals)
    goals_vectorized = vectorizer.transform([goals_processed])

    user_data = pd.DataFrame({
        'interests': [data.interests],
        'education': [data.education],
        'study_availability': [data.study_availability],
        'work_availability': [data.work_availability],
        'goals': [goals_processed]
    })

    user_encoded = encoder.transform(user_data.drop(columns=['goals'])).toarray()
    user_combined = np.hstack([user_encoded, goals_vectorized.toarray()])
    recommendation = clf.predict(user_combined)

    return {"recommendation": recommendation[0]}