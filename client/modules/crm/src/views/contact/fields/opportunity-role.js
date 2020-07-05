

Espo.define('crm:views/contact/fields/opportunity-role', 'views/fields/enum', function (Dep) {

    return Dep.extend({

        searchTypeList: ['anyOf', 'noneOf']

    });

});
