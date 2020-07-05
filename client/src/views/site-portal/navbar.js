

define('views/site-portal/navbar', 'views/site/navbar', function (Dep) {

    return Dep.extend({

        getLogoSrc: function () {
            var companyLogoId = this.getConfig().get('companyLogoId');
            if (!companyLogoId) {
                return this.getBasePath() + (this.getThemeManager().getParam('logo') || 'client/img/logo.png');
            }
            return this.getBasePath() + '?entryPoint=LogoImage&id='+companyLogoId+'&t=' + companyLogoId;
        },

        getTabList: function () {
            var tabList = this.getConfig().get('tabList') || [];
            tabList = Espo.Utils.clone(tabList || []);

            if (this.getThemeManager().getParam('navbarIsVertical')) {
                tabList.unshift('Home');
            }
            return tabList;
        },

        getQuickCreateList: function () {
            return this.getConfig().get('quickCreateList') || []
        }

    });

});
