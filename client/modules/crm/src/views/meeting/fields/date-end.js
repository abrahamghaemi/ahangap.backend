

Espo.define('crm:views/meeting/fields/date-end', 'views/fields/datetime-optional', function (Dep) {

    return Dep.extend({

        validateAfterAllowSameDay: true,

        emptyTimeInInlineEditDisabled: true,

        noneOptionIsHidden: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:isAllDay', function (model, value, o) {
                if (!o.ui) return;
                if (!this.isEditMode()) return;

                if (value) {
                    this.$time.val(this.noneOption);
                } else {
                    var dateTime = this.model.get('dateStart');
                    if (!dateTime) {
                        dateTime = this.getDateTime().getNow(5);
                    }
                    var m = this.getDateTime().toMoment(dateTime);
                    dateTime = m.format(this.getDateTime().internalDateTimeFormat);
                    var index = dateTime.indexOf(' ');
                    var time = dateTime.substr(index + 1);

                    const isRtl = Espo.Utils.isRtl();
                    if (isRtl) {
                        this.$time = this.$time = this.$el.find('input.time-part');
                    }
                    this.$time.val(time);
                }
                this.trigger('change');
                this.controlTimePartVisibility();
            }, this);
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.isEditMode()) {
                this.controlTimePartVisibility();
            }
        },

        controlTimePartVisibility: function () {
            if (!this.isEditMode()) return;

            if (this.model.get('isAllDay')) {
                this.$time.addClass('hidden');
                this.$el.find('.time-picker-btn').addClass('hidden');
            } else {
                this.$time.removeClass('hidden');
                this.$el.find('.time-picker-btn').removeClass('hidden');
            }
        }

    });
});
