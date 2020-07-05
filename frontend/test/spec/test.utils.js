

describe('utils', function () {
    var Utils;

    beforeEach(function (done) {
        require(['utils'], function (UtilsD) {
            Utils = UtilsD;
            done();
        });
    });

	it('#upperCaseFirst should make first letter upercase', function () {
		expect(Utils.upperCaseFirst('someTest')).toBe('SomeTest');
	});

    it('#checkAccessDataList should check access 1', function () {
        var user = {
            isPortal:  function () {},
            getLinkMultipleIdList: function () {},
            isAdmin: function () {}
        };
        var acl = {
            check: function () {},
            checkScope: function () {}
        };
        var entity = {
            name: 'Account'
        };
        spyOn(user, 'getLinkMultipleIdList').and.callFake(function (link) {
            if (link === 'teams') {
                return ['team1', 'team2'];
            }
            if (link === 'portals') {
                return ['portal1', 'portal2'];
            }
        });
        spyOn(user, 'isPortal').and.returnValue(false);
        spyOn(user, 'isAdmin').and.returnValue(false);

        spyOn(acl, 'checkScope').and.callFake(function (scope) {
            if (scope === 'Account') {
                return true;
            }
        });
        spyOn(acl, 'check').and.callFake(function (obj, action) {
            if (obj === 'Account') {
                return {
                    create: true,
                    read: true,
                    edit: true,
                    delete: false
                }[action];
            }
            if (obj.name) {
                return {
                    create: true,
                    read: true,
                    edit: false,
                    delete: false
                }[action];
            }
            return false;
        });

        expect(Utils.checkAccessDataList([
        ], acl, user)).toBe(true);

        expect(Utils.checkAccessDataList([
            {
                scope: 'Account'
            }
        ], acl, user)).toBe(true);

        expect(Utils.checkAccessDataList([
            {
                scope: 'Account',
                action: 'delete'
            }
        ], acl, user)).toBe(false);

        expect(Utils.checkAccessDataList([
            {
                scope: 'Account',
                action: 'read'
            },
            {
                inPortalDisabled: true
            }
        ], acl, user)).toBe(true);

        expect(Utils.checkAccessDataList([
            {
                action: 'read'
            },
            {
                teamIdList: ['team1', 'team3']
            }
        ], acl, user, entity)).toBe(true);

        expect(Utils.checkAccessDataList([
            {
                isPortalOnly: true
            }
        ], acl, user)).toBe(false);

        expect(Utils.checkAccessDataList([
            {
                isAdminOnly: true
            }
        ], acl, user)).toBe(false);

        expect(Utils.checkAccessDataList([
            {
                action: 'edit'
            }
        ], acl, user, entity)).toBe(false);
    });

    it('#checkAccessDataList should check access 2', function () {
        var user = {
            isPortal:  function () {},
            getLinkMultipleIdList: function () {},
            isAdmin: function () {}
        };
        var acl = {
            check: function () {},
            checkScope: function () {}
        };
        var entity = {
            name: 'Account'
        };
        spyOn(user, 'getLinkMultipleIdList').and.callFake(function (link) {
            if (link === 'teams') {
                return ['team1', 'team2'];
            }
            if (link === 'portals') {
                return ['portal1', 'portal2'];
            }
        });
        spyOn(user, 'isPortal').and.returnValue(true);
        spyOn(user, 'isAdmin').and.returnValue(false);

        expect(Utils.checkAccessDataList([
            {
                isPortalOnly: true
            }
        ], acl, user)).toBe(true);

        expect(Utils.checkAccessDataList([
            {
                portalIdList: ['portal2', 'portal3']
            }
        ], acl, user)).toBe(true);
    });


    it('#checkAccessDataList should check access 3', function () {
        var user = {
            isPortal:  function () {},
            getLinkMultipleIdList: function () {},
            isAdmin: function () {}
        };
        var acl = {
            check: function () {},
            checkScope: function () {}
        };
        var entity = {
            name: 'Account'
        };
        spyOn(user, 'getLinkMultipleIdList').and.callFake(function (link) {
            if (link === 'teams') {
                return ['team1', 'team2'];
            }
            if (link === 'portals') {
                return ['portal1', 'portal2'];
            }
        });
        spyOn(user, 'isPortal').and.returnValue(false);
        spyOn(user, 'isAdmin').and.returnValue(true);

        expect(Utils.checkAccessDataList([
            {
                portalIdList: ['portal3']
            }
        ], acl, user, null, true)).toBe(true);

        expect(Utils.checkAccessDataList([
            {
                teamIdList: ['team3']
            }
        ], acl, user, null, true)).toBe(true);
    });
});
