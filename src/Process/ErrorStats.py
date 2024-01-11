import pandas as pd
import numpy as np
import sys
import os
import regex as re
import matplotlib.pyplot as plt
from os.path import exists
import mysql.connector
from mysql.connector import Error
from ConnectDb import create_db_connection

# plotly
import plotly.figure_factory as ff
import plotly.express as px
import plotly.graph_objects as go

class GetError():
        
    def compareVersion(self, version, version_test):
        version_split = version.split(".")
        prefix = int(version_split[0])
        suffix = int(version_split[1])

        version_split_test = version_test.split(".")
        prefix_test = int(version_split_test[0])
        suffix_test = int(version_split_test[1])

        if (prefix_test > prefix or (prefix_test == prefix and suffix_test >= suffix)):
            return True
    
    def getData(self, filename, device_type, path):

        with open(path+"/"+device_type+"/"+filename+".txt", encoding="utf8", errors='ignore') as f:
            doc = f.read()
            chunk_array = re.split(r'[Mm]ain version:(\d{1,3}\.\d{1,3})', doc)
            new_chunk_array = []
            space = 20-len(filename)
            filename += " "*space
            if not re.match(r"(\d{1,3}\.\d{1,3})", chunk_array[0]):
                chunk_array.remove(chunk_array[0])
            for i in range(0, len(chunk_array), 2): # pair version number with date and error
                if re.match(r"(\d{1,3}\.\d{1,3})", chunk_array[i]) and not re.match('(Main version:\d{1,3}.\d{1,3})', chunk_array[i+1]):
                    if device_type == "BACK4":
                        from_version = "3.11"
                        if self.compareVersion(from_version, chunk_array[i])==True: # if version is greater than 3.11
                            version = chunk_array[i]
                            date = re.findall(r"(\d{2}_\d{2}_\d{2} \d{2}:\d{2}:\d{2}) \| \*{3}ERROR \(\d\) \| state:\d{1,3}", chunk_array[i+1])
                            error = re.findall(r"ERROR \(\d\) \| state:(\d{1,3})", chunk_array[i+1])  
                            if len(date) != 0 and len(error) != 0:
                                error_array = {"sn_id":filename, "version":version, "date":date[0], "error_id":error[0]} # we get only the first occurence to avoid repetitions due to device not switch off
                                new_chunk_array.append(error_array)
                
                    elif device_type == "BACK3":
                        from_version = "3.3"
                        if self.compareVersion(from_version, chunk_array[i])==True: # if version is greater than 3.3
                        #if re.match(r'(Main version:[3-9].\d[1-9]|Main version:[3-9].[1-9]0)|(?:Main version:)[3-9].[3-9]|(?:Main version:)[4-9].\d', chunk_array[i]):
                            version = chunk_array[i]
                            date = re.findall(r"(\d{2}_\d{2}_\d{2} \d{2}:\d{2}:\d{2}) \| \*{3}ERROR \(\d\) \| state:\d{1,3}", chunk_array[i+1])
                            error = re.findall(r"ERROR \(\d\) \| state:(\d{1,3})", chunk_array[i+1])
                            if len(date) != 0 and len(error) != 0:
                                error_array = {"sn_id":filename, "version":version, "date":date[0], "error_id":error[0]}
                                new_chunk_array.append(error_array)
                    
            return new_chunk_array

    def toDataframe(self, device_type, path):
        file_list = os.listdir(path+"/"+device_type+'/')
        error_array = []
        for file in file_list:
            file = file[:-4]
            chunk_array = self.getData(file, device_type, path)
            error_array += chunk_array
        df_error_log = pd.DataFrame(error_array)
        for date in df_error_log['date']:
            #if date.startswith("32"):
            if int(date[0:2])>31:
                #print(date)
                df_error_log.drop(df_error_log[df_error_log['date'] == date].index, inplace = True)
        df_error_log['date'] = pd.to_datetime(df_error_log['date'], dayfirst=True, format='%d_%m_%y %H:%M:%S')
        df_error_log['date'] = df_error_log['date'].astype(str)
        return df_error_log
    
    def execute_list_query(self, connection, sql, val):
        cursor = connection.cursor()
        #print(sql)
        try:
            cursor.executemany(sql, val)
            connection.commit()
            #print("Query successful")
        except Error as err:
            print(f"Error: '{err}'")

    def chunker(self, seq, size):
        return (seq[pos:pos + size] for pos in range(0, len(seq), size))
    
    def writeSql(self, df, table_name):
        """Formuler la requete

        Args:
            df (_type_): _description_
            table_name (_type_): _description_

        Returns:
            _type_: _description_
        """
        C=''
        val=''
        for col in df.columns:
            C+="`"+col+"`, "
            val+="%s, "
        sql= "INSERT INTO "+table_name+"(" + C[:-2] + ") SELECT " + val[:-2]
        print(sql)
        return sql
       
    #def toSql(self, df_error_log):
        # register data in database

        # see how to register new data and delete old data: 18months lifetime

    def countValue(self, df_error_log):
        value_counts = df_error_log["error_id"].value_counts()
        df_val_counts = pd.DataFrame(value_counts)
        df_val_counts.columns = ['counts']
        df_val_counts.reset_index(inplace=True)
        return df_val_counts
    
    def countBySn(self, df_error_log):
        value_counts = df_error_log.groupby(["sn_id"])["error_id"].value_counts()
        df_val_counts = pd.DataFrame(value_counts)
        df_val_counts.columns = ['counts']
        df_val_counts.reset_index(inplace=True)
        return df_val_counts
    
    def toResult(self, df_val_counts):
        error_list = df_val_counts.error_id.unique()
        sn_list = df_val_counts.sn_id.unique()
        result_array = []
        for sn in sn_list:
            sn_dict = {'sn_id': sn}
            for err in error_list:
                if not df_val_counts.error_id[(df_val_counts.sn_id == sn) & (df_val_counts.error_id == err)].empty:
                    sn_dict[err]=df_val_counts.counts[(df_val_counts.sn_id == sn) & (df_val_counts.error_id == err)].values[0]
                else:
                    sn_dict[err]=""
            result_array.append(sn_dict)
        df_result = pd.DataFrame(result_array)
        return df_result

    def main(self, deviceType, connection, file_path):
        #file_path = os.getcwd()
        file_path_dir = file_path+'/public/Ressource/logs'
        error_log = self.toDataframe(deviceType, file_path_dir)
        error_count = self.countValue(error_log)
        val_counts = self.countBySn(error_log)
        df_result = self.toResult(val_counts)
        with pd.ExcelWriter(file_path+'/public/Ressource/scripts/'+"errorStats_"+deviceType+".xlsx") as writer:
            error_log.to_excel(writer, sheet_name='error_log')
            error_count.to_excel(writer, sheet_name='error_count')
            val_counts.to_excel(writer, sheet_name='sn')
            df_result.to_excel(writer, sheet_name='sn_detail')

        # toSql
        values_to_insert = error_log.values.tolist()
        #connection = self.connect('localhost', 'root', '', 'winback_dev')
        sql_result = self.writeSql(error_log, "error")
        for group in self.chunker(values_to_insert, 10):
            try:
                self.execute_list_query(connection, sql_result, group)
            except mysql.connector.Error as err:
                print("Something went wrong: {}".format(err))
        return df_result

if __name__ == "__main__":
    getError = GetError()
    connection = create_db_connection()
    #deviceType = "BACK4"
    deviceType = sys.argv[1]
    file_path = os.getenv('ROOT_ABS')
    getError.main(deviceType, connection, file_path)