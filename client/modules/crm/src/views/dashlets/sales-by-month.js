

Espo.define('crm:views/dashlets/sales-by-month', 'crm:views/dashlets/abstract/chart', function (Dep) {

    return Dep.extend({

        name: 'SalesByMonth',

        columnWidth: 50,

        setupDefaultOptions: function () {
            this.defaultOptions['dateFrom'] = this.defaultOptions['dateFrom'] || moment().format('YYYY') + '-01-01';
            this.defaultOptions['dateTo'] = this.defaultOptions['dateTo'] || moment().format('YYYY') + '-12-31';
        },

        url: function () {
            var url = 'Opportunity/action/reportSalesByMonth?dateFilter='+ this.getDateFilter();

            if (this.getDateFilter() === 'between') {
                url += '&dateFrom=' + this.getOption('dateFrom') + '&dateTo=' + this.getOption('dateTo');
            }
            return url;
        },

        getLegendHeight: function () {
            return 0;
        },

        isNoData: function () {
            return !this.monthList.length;
        },

        prepareData: function (response) {
            var monthList = this.monthList = response.keyList;

            var dataMap = response.dataMap || {};

            var values = [];

            monthList.forEach(function (month) {
                values.push(dataMap[month]);
            }, this);

            this.chartData = [];

            var mid = 0;
            if (values.length) {
                mid = values.reduce(function(a, b) {return a + b}) / values.length;
            }

            var data = [];

            var max = 0;

            values.forEach(function (value, i) {
                if (value && value > max) {
                    max = value;
                }
                data.push({
                    data: [[i, value]],
                    color: (value >= mid) ? this.successColor : this.colorBad
                });
            }, this);

            this.max = max;

            return data;
        },

        setup: function () {
            this.currency = this.getConfig().get('defaultCurrency');
            this.currencySymbol = this.getMetadata().get(['app', 'currency', 'symbolMap', this.currency]) || '';

            this.colorBad = this.successColor;
        },

        getTickNumber: function () {
            var containerWidth = this.$container.width();
            var tickNumber = Math.floor(containerWidth / this.columnWidth);

            return tickNumber;
        },

        draw: function () {
            var self = this;
            var tickNumber = this.getTickNumber();

            this.flotr.draw(this.$container.get(0), this.chartData, {
                shadowSize: false,
                bars: {
                    show: true,
                    horizontal: false,
                    shadowSize: 0,
                    lineWidth: 1,
                    fillOpacity: 1,
                    barWidth: 0.5
                },
                grid: {
                    horizontalLines: true,
                    verticalLines: false,
                    outline: 'sw',
                    color: this.gridColor,
                    tickColor: this.tickColor
                },
                yaxis: {
                    min: 0,
                    showLabels: true,
                    color: this.textColor,
                    max: this.max + 0.08 * this.max,
                    tickFormatter: function (value) {
                        if (value == 0) {
                            return '';
                        }
                        if (value % 1 == 0) {
                            return self.currencySymbol + self.formatNumber(Math.floor(value)).toString();
                        }
                        return '';
                    }
                },
                xaxis: {
                    min: 0,
                    color: this.textColor,
                    noTicks: tickNumber,
                    tickFormatter: function (value) {
                        if (value % 1 == 0) {
                            var i = parseInt(value);
                            if (i in self.monthList) {
                                if (self.monthList.length - tickNumber > 5 && i === self.monthList.length - 1) {
                                    return '';
                                }
                                return moment(self.monthList[i] + '-01').format('MMM YYYY');
                            }
                        }
                        return '';
                    }
                },
                mouse: {
                    track: true,
                    relative: true,
                    lineColor: this.hoverColor,
                    position: 's',
                    autoPositionVertical: true,
                    trackFormatter: function (obj) {
                        var i = parseInt(obj.x);
                        var value = '';
                        if (i in self.monthList) {
                            value += moment(self.monthList[i] + '-01').format('MMM YYYY') + '<br>';
                        }
                        return value + self.currencySymbol + self.formatNumber(obj.y, true);
                    }
                }
            })
        }
    });
});
