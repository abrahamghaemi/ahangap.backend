

Espo.define('views/fields/datetime-optional', 'views/fields/datetime', function (Dep) {

    return Dep.extend({

        type: "datetimeOptional",

        setup: function () {
            this.noneOption = this.translate('None');
            this.nameDate = this.name + 'Date';
        },

        isDate: function () {
            var dateValue = this.model.get(this.nameDate);
            if (dateValue && dateValue != '') {
                return true;
            }
            return false;
        },

        data: function () {
            var data = Dep.prototype.data.call(this);
            if (this.isDate()) {
                var dateValue = this.model.get(this.nameDate);
                data.date = this.getDateTime().toDisplayDate(dateValue);
                data.time = this.noneOption;
            }
            return data;
        },

        getDateStringValue: function () {
            if (this.isDate()) {
                var dateValue = this.model.get(this.nameDate);
                return this.stringifyDateValue(dateValue);
            }
            return Dep.prototype.getDateStringValue.call(this);
        },

        setDefaultTime: function () {
            this.$time.val(this.noneOption);
        },

        initTimepicker: function () {
            var $time = this.$time;

            var o = {
                step: this.params.minuteStep || 30,
                scrollDefaultNow: true,
                timeFormat: this.timeFormatMap[this.getDateTime().timeFormat],
                noneOption: [{
                    label: this.noneOption,
                    value: this.noneOption,
                }]
            };

            if (this.emptyTimeInInlineEditDisabled && this.isInlineEditMode() || this.noneOptionIsHidden) {
                delete o.noneOption;
            }

            $time.timepicker(o);
            $time.parent().find('button.time-picker-btn').on('click', function () {
                $time.timepicker('show');
            });
        },

        fetch: function () {
            var data = {};
            const isRtl = Espo.Utils.isRtl();
            if (isRtl) {
                this.$date = this.$el.find('[data-name="' + this.name + '"]');
                this.$time = this.$time = this.$el.find('input.time-part');
            }

            var date = this.$date.val();
            var time = this.$time.val();

            var value = null;
            if (time != this.noneOption && time != '') {
                if (date != '' && time != '') {
                    value = this.parse(date + ' ' + time);
                }
                data[this.name] = value;
                data[this.nameDate] = null;
            } else {
                if (date != '') {
                    data[this.nameDate] = this.getDateTime().fromDisplayDate(date);
                    var dateTimeValue = data[this.nameDate] + ' 00:00:00';

                    dateTimeValue = moment.utc(dateTimeValue)
                        .tz(this.getConfig().get('timeZone') || 'UTC')
                        .format(this.getDateTime().internalDateTimeFullFormat);

                    data[this.name] = dateTimeValue;
                } else {
                    data[this.nameDate] = null;
                    data[this.name] = null;
                }
            }
            return data;
        },

        validateAfter: function () {
            var field = this.model.getFieldParam(this.name, 'after');
            if (field) {
                var fieldDate  = field + 'Date';
                var value = this.model.get(this.name) || this.model.get(this.nameDate);
                var otherValue = this.model.get(field) || this.model.get(fieldDate);
                if (value && otherValue) {
                    var isNotValid = false;
                    if (this.validateAfterAllowSameDay && this.model.get(this.nameDate)) {
                        isNotValid = moment(value).unix() < moment(otherValue).unix();
                    } else {
                        isNotValid = moment(value).unix() <= moment(otherValue).unix();
                    }
                    if (isNotValid) {
                        var msg = this.translate('fieldShouldAfter', 'messages').replace('{field}', this.getLabelText())
                                                                                .replace('{otherField}', this.translate(field, 'fields', this.model.name));

                        this.showValidationMessage(msg);
                        return true;
                    }
                }
            }
        },

        validateBefore: function () {
            var field = this.model.getFieldParam(this.name, 'before');
            if (field) {
                var fieldDate  = field + 'Date';
                var value = this.model.get(this.name) || this.model.get(this.nameDate);
                var otherValue = this.model.get(field) || this.model.get(fieldDate);
                if (value && otherValue) {
                    if (moment(value).unix() >= moment(otherValue).unix()) {
                        var msg = this.translate('fieldShouldBefore', 'messages').replace('{field}', this.getLabelText())
                                                                                 .replace('{otherField}', this.translate(field, 'fields', this.model.name));
                        this.showValidationMessage(msg);
                        return true;
                    }
                }
            }
        },

        validateRequired: function () {
            if (this.isRequired()) {
                if (this.model.get(this.name) === null && this.model.get(this.nameDate) === null) {
                    var msg = this.translate('fieldIsRequired', 'messages').replace('{field}', this.getLabelText());
                    this.showValidationMessage(msg);
                    return true;
                }
            }
        }

    });
});
