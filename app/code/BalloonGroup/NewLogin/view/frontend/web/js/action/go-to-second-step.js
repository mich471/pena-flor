define([
    'jquery',
    'mage/url',
    'BalloonGroup_NewLogin/js/model/first-step',
    'BalloonGroup_NewLogin/js/model/second-step'
], function (
    $,
    mageUrl,
    firstStep, 
    secondStep
) {
    'use strict';

    /** 
    * @return {Boolean}
    */
    function canGoToNextStep() {
        if (!firstStep.companyId()) return false;
        const companies = firstStep.getCompaniesList().map(company => company.label);
        if (!companies.includes(firstStep.companyName())) return false;
        return true;
    }

    /** 
    * @param {function}
    * @return {jQuery.jqXHR}
    */
    function goToNextStep(goToNextStepCallback) {
        $('body').trigger('processStart');
        const requestString = 'newlogin/emailDomain/company/id/' + firstStep.companyId();
        const url = mageUrl.build(requestString);
        return $.ajax({
            url: url,
            dataType: 'json',
            showLoader: true
        })
            .done(data => {
                secondStep.state.companyDomains(data);
                goToNextStepCallback();
            })
            .always(() => $('body').trigger('processStop'));
    }

    return {
        canGoToNextStep: canGoToNextStep,
        goToNextStep: goToNextStep
    };
});