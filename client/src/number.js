

define('number', [], function () {

    var NumberUtil = function (config, preferences) {
        this.config = config;
        this.preferences = preferences;

        this.thousandSeparator = null;
        this.decimalMark = null;

        this.config.on('change', function () {
            this.thousandSeparator = null;
            this.decimalMark = null;
        }, this);

        this.preferences.on('change', function () {
            this.thousandSeparator = null;
            this.decimalMark = null;
        }, this);

        this.maxDecimalPlaces = 10;
    };

    _.extend(NumberUtil.prototype, {

        formatInt: function (value) {
            if (value === null || value === undefined) return '';

            var stringValue = value.toString();
            stringValue = stringValue.replace(/\B(?=(\d{3})+(?!\d))/g, this.getThousandSeparator());
            return stringValue;
        },

        formatFloat: function (value, decimalPlaces) {
            if (value === null || value === undefined) return '';

            if (decimalPlaces === 0) {
                value = Math.round(value);
            } else if (decimalPlaces) {
                value = Math.round(value * Math.pow(10, decimalPlaces)) / (Math.pow(10, decimalPlaces));
            } else {
                value = Math.round(value * Math.pow(10, this.maxDecimalPlaces)) / (Math.pow(10, this.maxDecimalPlaces));
            }

            var parts = value.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, this.getThousandSeparator());

            if (decimalPlaces === 0) {
                return parts[0];
            } else if (decimalPlaces) {
                var decimalPartLength = 0;
                if (parts.length > 1) {
                    decimalPartLength = parts[1].length;
                } else {
                    parts[1] = '';
                }

                if (decimalPlaces && decimalPartLength < decimalPlaces) {
                    var limit = decimalPlaces - decimalPartLength;
                    for (var i = 0; i < limit; i++) {
                        parts[1] += '0';
                    }
                }
            }

            return parts.join(this.getDecimalMark());

        },

        getThousandSeparator: function () {
            if (this.thousandSeparator !== null) return this.thousandSeparator;

            var thousandSeparator = '.';
            if (this.preferences.has('thousandSeparator')) {
                thousandSeparator = this.preferences.get('thousandSeparator');
            } else {
                if (this.config.has('thousandSeparator')) {
                    thousandSeparator = this.config.get('thousandSeparator');
                }
            }
            this.thousandSeparator = thousandSeparator;

            return thousandSeparator;
        },

        getDecimalMark: function () {
            if (this.decimalMark !== null) return this.decimalMark;

            var decimalMark = '.';
            if (this.preferences.has('decimalMark')) {
                decimalMark = this.preferences.get('decimalMark');
            } else {
                if (this.config.has('decimalMark')) {
                    decimalMark = this.config.get('decimalMark');
                }
            }
            this.decimalMark = decimalMark;

            return decimalMark;
        },

    });

    return NumberUtil;
});
