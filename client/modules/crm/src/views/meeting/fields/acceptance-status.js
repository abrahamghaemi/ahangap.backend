

Espo.define('crm:views/meeting/fields/acceptance-status', 'views/fields/enum', function (Dep) {

    return Dep.extend({

        searchTypeList: ['anyOf', 'noneOf']

    });

});
