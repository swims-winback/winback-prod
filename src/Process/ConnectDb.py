import os
from dotenv import load_dotenv
import mysql.connector
from mysql.connector import Error


        
def create_db_connection():
    """CONNECTION

    Args:
        host_name (_type_): _description_
        user_name (_type_): _description_
        user_password (_type_): _description_
        db_name (_type_): _description_

    Returns:
        _type_: _description_
    """
    connection = None
    hostname = os.getenv('DB_HOSTNAME')
    admin = os.getenv('DB_ADMIN')
    pwd = os.getenv('DB_PWD')
    db = os.getenv('DB_NAME')
    try:
        connection = mysql.connector.connect(
            host=hostname,
            user=admin,
            passwd=pwd,
            database=db
        )
        print("MySQL Database connection successful")
    except Error as err:
        print(f"Error: '{err}'")

    return connection

