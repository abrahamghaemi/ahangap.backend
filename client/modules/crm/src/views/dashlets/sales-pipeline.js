

Espo.define('crm:views/dashlets/sales-pipeline', 'crm:views/dashlets/abstract/chart', function (Dep) {

    return Dep.extend({

        name: 'SalesPipeline',

        setupDefaultOptions: function () {
            this.defaultOptions['dateFrom'] = this.defaultOptions['dateFrom'] || moment().format('YYYY') + '-01-01';
            this.defaultOptions['dateTo'] = this.defaultOptions['dateTo'] || moment().format('YYYY') + '-12-31';
        },

        url: function () {
            var url = 'Opportunity/action/reportSalesPipeline?dateFilter='+ this.getDateFilter();

            if (this.getDateFilter() === 'between') {
                url += '&dateFrom=' + this.getOption('dateFrom') + '&dateTo=' + this.getOption('dateTo');
            }

            if (this.getOption('useLastStage')) {
                url += '&useLastStage=true';
            }
            return url;
        },

        prepareData: function (response) {
            var d = [];

            response.dataList.forEach(function (item) {
                d.push({
                    stageTranslated: this.getLanguage().translateOption(item.stage, 'stage', 'Opportunity'),
                    value: item.value,
                    stage: item.stage
                });
            }, this);

            var data = [];
            for (var i = 0; i < d.length; i++) {
                var item = d[i];
                var value = item.value;
                var nextValue = ((i + 1) < d.length) ? d[i + 1].value : value;
                data.push({
                    data: [[i, value], [i + 1, nextValue]],
                    label: item.stageTranslated,
                    stage: item.stage
                });
            }

            var max = 0;
            if (d.length) {
                d.forEach(function (item) {
                    if ( item.value && item.value > max) {
                        max = item.value;
                    }
                }, this);
            }
            this.max = max;

            return data;
        },

        setup: function () {
            this.currency = this.getConfig().get('defaultCurrency');
            this.currencySymbol = this.getMetadata().get(['app', 'currency', 'symbolMap', this.currency]) || '';

            this.chartData = [];
        },

        draw: function () {
            var self = this;

            var colors = Espo.Utils.clone(this.colorList);

            this.chartData.forEach(function (item, i) {
                if (i + 1 > colors.length) {
                    colors.push('#164');
                }
                if (this.chartData.length == i + 1 && item.stage === 'Closed Won') {
                    colors[i] = this.successColor;
                }
            }, this);


            this.flotr.draw(this.$container.get(0), this.chartData, {
                colors: colors,
                shadowSize: false,
                lines: {
                    show: true,
                    fill: true,
                    fillOpacity: 1
                },
                points: {
                    show: true
                },
                grid: {
                    color: this.tickColor,
                    verticalLines: false,
                    outline: '',
                    tickColor: this.tickColor
                },
                yaxis: {
                    min: 0,
                    max: this.max + 0.08 * this.max,
                    showLabels: true,
                    color: this.textColor,
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
                    showLabels: false
                },
                mouse: {
                    track: true,
                    relative: true,
                    position: 'n',
                    lineColor: this.hoverColor,
                    trackFormatter: function (obj) {
                        if (obj.x >= self.chartData.length) {
                            return null;
                        }
                        var label = self.chartData[parseInt(obj.x)].label;
                        var label = (label || self.translate('None'));
                        return label  + '<br>' + self.currencySymbol + self.formatNumber(obj.y, true);
                    }
                },
                legend: {
                    show: true,
                    noColumns: this.getLegendColumnNumber(),
                    container: this.$el.find('.legend-container'),
                    labelBoxMargin: 0,
                    labelFormatter: self.labelFormatter.bind(self),
                    labelBoxBorderColor: 'transparent',
                    backgroundOpacity: 0
                }
            });

            this.adjustLegend();
        }

    });
});
