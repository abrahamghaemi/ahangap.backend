

 define('field-manager', [], function () {

    var FieldManager = function (defs, metadata) {
        this.defs = defs || {};
        this.metadata = metadata;
    };

    _.extend(FieldManager.prototype, {

        defs: null,

        metadata: null,

        getParamList: function (fieldType) {
            if (fieldType in this.defs) {
                return this.defs[fieldType].params || [];
            }
            return [];
        },

        checkFilter: function (fieldType) {
            if (fieldType in this.defs) {
                if ('filter' in this.defs[fieldType]) {
                    return this.defs[fieldType].filter;
                } else {
                    return false;
                }
            }
            return false;
        },

        isMergeable: function (fieldType) {
            if (fieldType in this.defs) {
                return !this.defs[fieldType].notMergeable;
            }
            return false;
        },

        getEntityTypeAttributeList: function (entityType) {
            var list = [];
            var defs = this.metadata.get('entityDefs.' + entityType + '.fields') || {};
            Object.keys(defs).forEach(function (field) {
                this.getAttributeList(defs[field]['type'], field).forEach(function (attr) {
                    if (!~list.indexOf(attr)) {
                        list.push(attr);
                    }
                });
            }, this);
            return list;
        },

        getActualAttributeList: function (fieldType, fieldName) {
            var fieldNames = [];
            if (fieldType in this.defs) {
                if ('actualFields' in this.defs[fieldType]) {
                    var actualfFields = this.defs[fieldType].actualFields;

                    var naming = 'suffix';
                    if ('naming' in this.defs[fieldType]) {
                        naming = this.defs[fieldType].naming;
                    }
                    if (naming == 'prefix') {
                        actualfFields.forEach(function (f) {
                            fieldNames.push(f + Espo.Utils.upperCaseFirst(fieldName));
                        });
                    } else {
                        actualfFields.forEach(function (f) {
                            fieldNames.push(fieldName + Espo.Utils.upperCaseFirst(f));
                        });
                    }
                } else {
                    fieldNames.push(fieldName);
                }
            }
            return fieldNames;
        },

        getNotActualAttributeList: function (fieldType, fieldName) {
            var fieldNames = [];
            if (fieldType in this.defs) {
                if ('notActualFields' in this.defs[fieldType]) {
                    var notActualFields = this.defs[fieldType].notActualFields;

                    var naming = 'suffix';
                    if ('naming' in this.defs[fieldType]) {
                        naming = this.defs[fieldType].naming;
                    }
                    if (naming == 'prefix') {
                        notActualFields.forEach(function (f) {
                            if (f === '') {
                                fieldNames.push(fieldName);
                            } else {
                                fieldNames.push(f + Espo.Utils.upperCaseFirst(fieldName));
                            }
                        });
                    } else {
                        notActualFields.forEach(function (f) {
                            fieldNames.push(fieldName + Espo.Utils.upperCaseFirst(f));
                        });
                    }
                }
            }
            return fieldNames;
        },

        getEntityTypeFieldAttributeList: function (entityType, field) {
            var type = this.metadata.get(['entityDefs', entityType, 'fields', field, 'type']);
            if (!type) return [];
            return this.getAttributeList(type, field);
        },

        getAttributeList: function (fieldType, fieldName) {
            return _.union(this.getActualAttributeList(fieldType, fieldName), this.getNotActualAttributeList(fieldType, fieldName));
        },

        getEntityTypeFieldList: function (entityType) {
            return Object.keys(this.metadata.get(['entityDefs', entityType, 'fields']) || {});
        },

        getScopeFieldList: function (entityType) { // TODO remove in 5.8.0
            return this.getEntityTypeFieldList(entityType);
        },

        getEntityTypeFieldParam: function (entityType, field, param) {
            this.metadata.get(['entityDefs', entityType, 'fields', field, param]);
        },

        getViewName: function (fieldType) {
            if (fieldType in this.defs) {
                if ('view' in this.defs[fieldType]) {
                    return this.defs[fieldType].view;
                }
            }
            return 'views/fields/' + Espo.Utils.camelCaseToHyphen(fieldType);
        },

        getParams: function (fieldType) {
            return this.getParamList(fieldType);
        },

        getAttributes: function (fieldType, fieldName) {
            return this.getAttributeList(fieldType, fieldName);
        },

        getActualAttributes: function (fieldType, fieldName) {
            return this.getActualAttributeList(fieldType, fieldName);
        },

        getNotActualAttributes: function (fieldType, fieldName) {
            return this.getNotActualAttributeList(fieldType, fieldName);
        },

        isEntityTypeFieldAvailable: function (entityType, field) {
            if (this.metadata.get(['entityDefs', entityType, 'fields', field, 'disabled'])) return false;
            if (
                this.metadata.get(['entityAcl', entityType, 'fields', field, 'onlyAdmin'])
                ||
                this.metadata.get(['entityAcl', entityType, 'fields', field, 'forbidden'])
                ||
                this.metadata.get(['entityAcl', entityType, 'fields', field, 'internal'])
            ) return false;

            return true;
        },

        isScopeFieldAvailable: function (entityType, field) {
            return this.isEntityTypeFieldAvailable(entityType, field);
        },

    });

    return FieldManager;
});
