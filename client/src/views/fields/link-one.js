

Espo.define('views/fields/link-one', 'views/fields/link', function (Dep) {

    return Dep.extend({

        readOnly: true,

        searchTypeList: ['is', 'isOneOf'],

        fetchSearch: function () {
            var type = this.$el.find('select.search-type').val();
            var value = this.$el.find('[data-name="' + this.idName + '"]').val();

            if (type == 'isOneOf') {
                var data = {
                    type: 'linkedWith',
                    field: this.name,
                    value: this.searchData.oneOfIdList,
                    data: {
                        type: type,
                        oneOfIdList: this.searchData.oneOfIdList,
                        oneOfNameHash: this.searchData.oneOfNameHash
                    }
                };
                return data;

            } else {
                if (!value) {
                    return false;
                }
                var data = {
                    type: 'linkedWith',
                    field: this.name,
                    value: value,
                    data: {
                        type: type,
                        nameValue: this.$el.find('[data-name="' + this.nameName + '"]').val()
                    }
                };
                return data;
            }
        },

    });
});
