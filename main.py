from fastapi import FastAPI, HTTPException
import pandas as pd
import numpy as np
import mysql.connector

app = FastAPI()

def get_data_from_db():
    conn = mysql.connector.connect(
        host="localhost",
        user="id21118998_ppaupallares",
        password="NEVERmind38@",
        database="id21118998_db_hosting"
    )   

    df = pd.read_sql("SELECT * FROM modelTraining ORDER BY id  DESC LIMIT 1", conn)
    conn.close()
    return df

@app.get("/")
def read_root():
    return {"Hello": "World"}

@app.post("/recommend")
def recommend(data: dict):
    interests = data['interests']
    education = data['education']
    study_availability = data['study_availability']
    work_availability = data['work_availability']
    goals = data['goals']
    
    print("Intereses:", interests)
    print("Educación:", education)
    print("Disponibilidad de estudio:", study_availability)
    print("Disponibilidad de trabajo:", work_availability)
    print("Objetivos:", goals)
    
    return {"message": "Datos recibidos correctamente"}
