#coding:utf-8
import twder
import json
import datetime

class Exchange:
    def __init__(self):
        self.currencies = ('JPY','USD','CNY')
        self.currencyData = {'JPY':{},'USD':{},'CNY':{}}
        self.filePath = 'storage/app/public/exchangeRate.txt'
        today = datetime.date.today()
        first = today.replace(day=1)
        lastMonthLastDay = first - datetime.timedelta(days=1)

        self.getYear= int(lastMonthLastDay.strftime("%Y"))
        self.getMonth= int(lastMonthLastDay.strftime("%m"))

    def getExchangeLastMonth(self):

        for currency in self.currencies:
            self.currencyData[currency].update( {'"'+str( self.getYear)+str(self.getMonth)+'"':twder.specify_month( currency , self.getYear, self.getMonth )})

    def saveData(self):
        with open(self.filePath,"w") as f:
            f.write(json.dumps(self.currencyData))
    def getExchangeByRange(self,startYear,startMonth):

        for currency in self.currencies:
            tmpYear = int(startYear)
            tmpMonth = int(startMonth)
            stop = -1

            while stop != 1:
                if tmpYear == self.getYear and tmpMonth == self.getMonth:
                    stop = 1
                self.currencyData[currency].update( {'"'+str(tmpYear)+str(tmpMonth)+'"' : twder.specify_month( currency , tmpYear, tmpMonth )})
#                 self.currencyData[currency].update( {f'{tmpYear}{tmpMonth}' :twder.specify_month( currency , tmpYear, tmpMonth )})
                if tmpMonth == 12:
                    tmpYear += 1
                    tmpMonth = 1
                else:
                    tmpMonth += 1

exchangeRate = Exchange()
exchangeRate.getExchangeLastMonth()
# 台灣銀行查歷史匯率最多就一年期而已 超過就沒有資料囉
# exchangeRate.getExchangeByRange(2019,1)
exchangeRate.saveData()
