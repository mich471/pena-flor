define([
    'BalloonGroup_NewLogin/js/model/first-step',
    'BalloonGroup_NewLogin/js/model/second-step'
], function (
    firstStep,
    secondStep
) {
    'use strict';
    /**
    * @return {boolean}
    */
     function canGoToNextStep() {
        if(secondStep.isInputValid()) return true;
        return false;
    }

    /**
    * @param {function}
    */
    function goToNextStep(goToNextStepCallback) {
        setCompanyInfo();
        if(secondStep.state.isDni()) {
           setDniInfo();
        } else if(secondStep.state.isEmail()) {
           setEmailInfo();
        }
        document.querySelector(".form-create-account").style.display = "inline-block";
        goToNextStepCallback();
    }

    function setCompanyInfo() {
        setInputInfo('#nombre_empresa', firstStep.companyName());
        setInputInfo('#company_id', firstStep.companyId());
    }

    function setDniInfo() {
        unsetInputInfo('#email_address');
        setInputInfo('#document_id', secondStep.state.dni());
        setInputInfo('#is_dni', true);
    }

    function setEmailInfo() {
        unsetInputInfo('#document_id');
        setInputInfo('#email_address', secondStep.state.email());
        setInputInfo('#is_dni', false);
    }

    function setInputInfo(selector, value) {
        const input = document.querySelector(selector);
        input.value = value;
        input.disabled = true;
    }

    function unsetInputInfo(selector) {
        const input = document.querySelector(selector);
        input.value = "";
        input.disabled = false;
    }

    return {
        canGoToNextStep: canGoToNextStep,
        goToNextStep: goToNextStep
    };
});
