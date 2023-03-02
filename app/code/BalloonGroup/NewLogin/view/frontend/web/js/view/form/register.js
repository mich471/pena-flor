define([
    'jquery',
    'uiComponent',
    'ko',
    'BalloonGroup_NewLogin/js/action/go-to-second-step',
    'BalloonGroup_NewLogin/js/action/go-to-last-step',
    'BalloonGroup_NewLogin/js/model/first-step',
    'BalloonGroup_NewLogin/js/model/second-step'
], function ($, 
    Component, 
    ko, 
    goToSecondStepAction,
    goToLastStepAction,
    firstStep, 
    secondStep
) {
    'use strict';

    let self;
    const stepDescriptions = [
        'Selecciona tu empresa',
        'Selecciona una opción',
        'Completa tus datos'
    ];

    return Component.extend({
        firstStep: firstStep,
        secondStep: secondStep,
        currentStep: ko.observable(0),
        isLoading: ko.observable(false),
        initialize: function() {
            self = this;
            this._super();
            firstStep.setCompaniesList(JSON.parse(self.companiesData));
            return this;
        },
        initObservable: function() {
            this._super();
            this._initStepObservables();
            secondStep.subscribeToObservables();
            return this;
        },
        _initStepObservables: function() {
            this.stepDesciption = ko.computed(function() {
                return stepDescriptions[this.currentStep()];
            }, this);
            this.isFirstStep = ko.computed(function() {
                return this.currentStep() == 0;
            }, this);
            this.isSecondStep = ko.computed(function() {
                return this.currentStep() == 1;
            }, this);
            this.isLastStep = ko.computed(function() {
                return this.currentStep() == 2;
            }, this);
            this.isNotLastStep = ko.computed(function() {
                return this.currentStep() != 2;
            }, this);

            document.getElementById("goToPreviousStep").addEventListener("click", function( event ) {
                event.preventDefault();
                document.querySelector(".form-create-account").style.display = "none";
                self.goToPreviousStep();
            });
        },
        nextStepHandler: function() {
            if(self.isFirstStep()) {
                if(!goToSecondStepAction.canGoToNextStep()) return;
                return goToSecondStepAction.goToNextStep(self.goToNextStep);
            } else {
                if(!goToLastStepAction.canGoToNextStep()) return;
                console.log("can go to next step");
                return goToLastStepAction.goToNextStep(self.goToNextStep);
            }
        },
        goToNextStep: function() {
            return self.currentStep(self.currentStep() + 1);
        },
        goToPreviousStep: function() {
            console.log("clicked in register")
            return self.currentStep(self.currentStep() - 1);
        },
    });
});
