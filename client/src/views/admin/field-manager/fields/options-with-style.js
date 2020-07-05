

Espo.define('views/admin/field-manager/fields/options-with-style', 'views/admin/field-manager/fields/options', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.optionsStyleMap = this.model.get('style') || {};

            this.styleList = ['default', 'success', 'danger', 'warning', 'info', 'primary'];

            this.events['click [data-action="selectOptionItemStyle"]'] = function (e) {
                var $target = $(e.currentTarget);
                var style = $target.data('style');
                var value = $target.data('value').toString();

                this.changeStyle(value, style);
            };

        },

        changeStyle: function (value, style) {
            var valueInternal = value.replace(/"/g, '\\"');

            this.$el.find('[data-action="selectOptionItemStyle"][data-value="'+valueInternal+'"] .check-icon').addClass('hidden');
            this.$el.find('[data-action="selectOptionItemStyle"][data-value="'+valueInternal+'"][data-style="'+style+'"] .check-icon').removeClass('hidden');

            var $item = this.$el.find('.list-group-item[data-value="'+valueInternal+'"]').find('.item-text');

            this.styleList.forEach(function (item) {
                $item.removeClass('text-' + item);
            }, this);

            $item.addClass('text-' + style);

            if (style === 'default') {
                style = null;
            }
            this.optionsStyleMap[value] = style;
        },

        getItemHtml: function (value) {
            var html = Dep.prototype.getItemHtml.call(this, value);

            if (!value) return html;

            var valueSanitized = this.escapeValue(value);
            var valueInternal = this.escapeValue(value);

            var $item = $(html);

            var itemListHtml = '';
            var styleList = this.styleList;

            var styleMap = this.optionsStyleMap;

            var style = 'default';

            styleList.forEach(function (item) {
                var hiddenPart = ' hidden';
                if (styleMap[value] === item) {
                    hiddenPart = '';
                    style = item;
                } else {
                    if (item === 'default' && !styleMap[value]) {
                        hiddenPart = '';
                    }
                }
                var translated = this.getLanguage().translateOption(item, 'style', 'LayoutManager');
                var innerHtml = '<span class="check-icon fas fa-check pull-right'+hiddenPart+'"></span><div>'+translated+'</div>';
                itemListHtml += '<li><a href="javascript:" data-action="selectOptionItemStyle" data-style="'+item+'" data-value="'+valueInternal+'">'+innerHtml+'</a></li>'
            }, this);

            var dropdownHtml =
                '<div class="btn-group pull-right">' +
                '<button type="button" class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>' +
                '<ul class="dropdown-menu pull-right">'+itemListHtml+'</ul>' +
                '</div>';

            $item.find('.item-content > input').after($(dropdownHtml));

            $item.find('.item-text').addClass('text-' + style);

            $item.addClass('link-group-item-with-columns');

            return $item.get(0).outerHTML;
        },

        fetch: function () {
            var data = Dep.prototype.fetch.call(this);

            data.style = {};

            (data.options || []).forEach(function (item) {
                data.style[item] = this.optionsStyleMap[item] || null;
            }, this);

            return data;
        },

    });
});
