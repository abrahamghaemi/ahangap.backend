

Espo.define('crm:views/meeting/fields/date-start', 'views/fields/datetime-optional', function (Dep) {

    return Dep.extend({

        emptyTimeInInlineEditDisabled: true,

        setup: function () {
            Dep.prototype.setup.call(this);
            this.noneOption = this.translate('All-Day', 'labels', 'Meeting');
        },

        fetch: function () {
            var data = Dep.prototype.fetch.call(this);

            if (data[this.nameDate]) {
                data.isAllDay = true;
            } else {
                data.isAllDay = false;
            }

            return data;
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.isEditMode()) {
                this.controlTimePartVisibility();
            }
        },

        controlTimePartVisibility: function () {
            if (!this.isEditMode()) return;

            if (this.isInlineEditMode()) {
                if (this.model.get('isAllDay')) {
                    this.$time.addClass('hidden');
                    this.$el.find('.time-picker-btn').addClass('hidden');
                } else {
                    this.$time.removeClass('hidden');
                    this.$el.find('.time-picker-btn').removeClass('hidden');
                }
            }
        }

    });
});
