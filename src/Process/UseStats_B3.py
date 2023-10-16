import pandas as pd
import numpy as np
import sys
import os
import regex as re
import matplotlib.pyplot as plt
from os.path import exists
import mysql.connector
from mysql.connector import Error

import plotly.figure_factory as ff
import plotly.express as px
import plotly.graph_objects as gox

class UseStats_B3:
    def getPattern(self, path, file):
        final_list = []
        treatment_dict = {}
        treatment_dict2 = {}

        with open(path+"/"+file+".txt", encoding="utf8", errors='ignore') as f:
            doc = f.read()
            chunk_array = re.split(r"(\d{2}_\d{2}_\d{2} \d{2}:\d{2}:\d{2}) \| Treatment start", doc)
            if not re.match(r'(\d{2}_\d{2}_\d{2} \d{2}:\d{2}:\d{2})', chunk_array[0]):
                chunk_array.remove(chunk_array[0])
            for i in range(0, len(chunk_array), 2):
                date = chunk_array[i]

                parameters = re.findall(r"\{1,\d+,\d+,\d+\};", chunk_array[i+1])
                if len(parameters)!=0:
                    time = int(len(parameters)/60)
                    if time > 1:
                        if date[:-8] in treatment_dict:
                            treatment_dict[date[:-8]] += time
                        else:
                            treatment_dict[date[:-8]] = time
                        treatment_dict2[date] = time

        for key, value in zip(treatment_dict.keys(), treatment_dict.values()):
            final_dict = {}
            final_dict["sn"] = file
            final_dict["date"] = key
            final_dict["time"] = value
            final_list.append(final_dict)
        return final_list

    def getResult(self, path):
        file_list = os.listdir(path)
        final_list = []

        #for file in file_list[:10]:
        for file in file_list:
            file = file[:-4]
            final_list+=self.getPattern(path, file)

        df = pd.DataFrame(final_list)
        df['date'] = df['date'].str.strip()
        for date in df['date']:
            if int(date[0:2])>31 or int(date[0:2])==00:
                df.drop(df[df['date'] == date].index, inplace = True)
        df['date'] = pd.to_datetime(df['date'], dayfirst=True, format="%d_%m_%y", errors='coerce')
        df['date'] = df['date'].astype(str)
        for date in df['date']:
            if date=="NaT" or date=="Nan" or date=="":
                df.drop(df[df['date'] == date].index, inplace = True)
        return df

    def getYear(self, df):
        df_year = df
        df_year['year'] = ""
        df_year['year']= df_year["date"].str.slice(0,-6)
        df_year = df_year.drop(['date'], axis=1)

        df_year_count = df_year.groupby(["sn", "year"])["time"].sum().reset_index(name="time")
        df_year_count["time"] = df_year_count["time"].div(60).round()
        df_year_count["time"] = df_year_count["time"].astype('int')
        return df_year_count
    
    def getMonth(self, df):
        df_month = df
        df_month['year'] = ""
        df_month['year']=df_month["date"].str.slice(0, -6)
        df_month['month'] = ""
        df_month['month']= df_month["date"].str.slice(0, -3)
        
        df_month = df_month.drop(['date'], axis=1)
        df_month_count = df_month.groupby(["sn", "year", "month"])["time"].sum().reset_index(name="time")
        df_month_count["time"] = df_month_count["time"].div(60).round()
        df_month_count["time"] = df_month_count["time"].astype('int')
        return df_month_count

    def getWeek(self, df):
        df_week = df
        df_week['year'] = ""
        df_week['year']=df_week["date"].str.slice(0, -6)
        df_week['month'] = ""
        df_week['month']= df_week["date"].str.slice(0, -3)
        df_week['week'] = ""
        df_week["date"] = pd.to_datetime(df_week['date'], format="%Y-%m-%d")
        df_week["week"] = df_week['date'].dt.isocalendar().week
        df_week = df_week.drop(['date'], axis=1)
        
        df_week_count = df_week.groupby(["sn", "year", "week"])["time"].sum().reset_index(name="time")
        df_week_count["time"] = df_week_count["time"].div(60).round()
        df_week_count["time"] = df_week_count["time"].astype('int')

        return df_week_count

    def main(self):
        file_path = os.getcwd()
        print(file_path)
        file_path_dir = file_path+"\public\Ressource\logs\BACK3"
        #file_path_dir = "./input/BACK3/"
        df = self.getResult(file_path_dir)
        df_year = self.getYear(df)
        df_month = self.getMonth(df)
        df_week = self.getWeek(df)
        #with pd.ExcelWriter('./output/'+"useStats_B3.xlsx") as writer:
        with pd.ExcelWriter(file_path+'/public/Ressource/scripts/'+"useStats_B3.xlsx") as writer:
            df.to_excel(writer, sheet_name='days')
            df_month.to_excel(writer, sheet_name='month')
            df_year.to_excel(writer, sheet_name='year')
            df_week.to_excel(writer, sheet_name='week')

if __name__ == "__main__":
    useStats = UseStats_B3()
    useStats.main()