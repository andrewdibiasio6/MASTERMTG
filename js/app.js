/*
 Some code involving the mana slider is from the following link, though many changes have been made.
 
 http://onehungrymind.com/build-sweet-photo-slider-angularjs-animate/ 
 
 
 updated by JMH on March 9, 2015 at 8:40 PM
 
 Andrew DiBiasio andrew_dibiasio@student.uml.edu
 I am a Computer Science Major at the University of Massachusetts Lowell. 
 I created this file as part of an assignment for my course 91.462 GUI Programming II. 
 This is my Java Script file used with my mana selector file. This file provides anmimation to my mana slider 
 using Greenshock animation libraries as well as uses scripts to fill out my form's checklist.
 
 Created on Feb 5, 2015
 */


// Injecting ngAnimate as a sub-module into the website module. Animations will now be available for the entire module.
angular.module('website', ['ngAnimate'])
        .controller('MainCtrl', function ($scope) {

          // An array of image objects that can be bound to in HTML to display the photos as well as navigation. 
          $scope.slides = [
            {image: 'images/red.png', description: 'red'},
            {image: 'images/green.png', description: 'green'},
            {image: 'images/blue.png', description: 'blue'},
            {image: 'images/black.png', description: 'black'},
            {image: 'images/white.png', description: 'white'}
          ];

          // currentIndex is initialized to 0(first slide).
          $scope.currentIndex = 0;

          // Mutator function that allows us to assign currentIndex a new value.
          $scope.setCurrentSlideIndex = function (index) {
            $scope.currentIndex = index;
          };

          // Tests to see if the input value and currentIndex are equal. Returns a  boolean value.
          $scope.isCurrentSlideIndex = function (index) {
            return $scope.currentIndex === index;
          };

          //Increments currentIndex
          $scope.prevSlide = function () {
            $scope.shadowColor = "box-shadow: 0 0 15px "
            console.log("prevSlide");

            $scope.currentIndex = ($scope.currentIndex < $scope.slides.length - 1) ? ++$scope.currentIndex : 0;

            // Changes shadow color of slider
            $scope.shadowColor += $scope.slides[$scope.currentIndex].description + ';"';
            document.getElementById("slider").setAttribute("style", $scope.shadowColor);
          }

          // Decrements currentIndex
          $scope.nextSlide = function () {
            $scope.shadowColor = "box-shadow: 0 0 15px "
            console.log("prevSlide");

            $scope.currentIndex = ($scope.currentIndex > 0) ? --$scope.currentIndex : $scope.slides.length - 1;

            // Changes shadow color of slider
            $scope.shadowColor += $scope.slides[$scope.currentIndex].description + ';"';
            document.getElementById("slider").setAttribute("style", $scope.shadowColor);
          };

          // buttonClick is envoked when a user clicks on the button #manaButton. It checks the corresponding check box for that color.
          $scope.buttonClick = function () {
            console.log("buttonClick fired: Current slide: discription:");
            console.log($scope.slides[$scope.currentIndex].description);
            if (document.getElementById($scope.slides[$scope.currentIndex].description).checked === true)
            {
              document.getElementById($scope.slides[$scope.currentIndex].description).checked = false;
              $scope.manaButtonClick($scope.slides[$scope.currentIndex].description);
            }
            else {
              document.getElementById($scope.slides[$scope.currentIndex].description).checked = true;
              $scope.manaButtonClick($scope.slides[$scope.currentIndex].description);
            }
          };

          $scope.active = false;

          // manaButtonClick is envoked when a user clicks the transparent button over the mana images or when checklist button is clicked.
          $scope.manaButtonClick = function (color) {
            $manaString = '';
            $manaString = $manaString + color + "Button";

            console.log($manaString);

            if (document.getElementById($manaString).getAttribute("active") === "true")
            {
              console.log("enteretd if");

              // change background of checklist button to default background
              document.getElementById($manaString).style.background = "#9C8AA5";

              // console.log(document.getElementById("redButton").hasAttribute("active"));
              document.getElementById($manaString).setAttribute("active", "false");
              document.getElementById(color).checked = false;
            }
            else
            {
              console.log("enteretd else");

              // change background of checklist button to correct color
              document.getElementById($manaString).style.background = color;

              //document.getElementById("redButton").setAttribute("class", "active");
              document.getElementById($manaString).setAttribute("active", "true");
              document.getElementById(color).checked = true;
            }
          };
        })

        // Adds animation to slides.
        .animation('.slide-animation', function () {
          return {
            //this handler is called when ng-hide is called 
            addClass: function (element, className, done) {
              if (className == 'ng-hide') {

                // Animate for half a second. The element is moved to left the entire width of its parent element so that it is no longer visible. 
                TweenMax.to(element, 0.5, {left: -element.parent().width(), onComplete: done});
              }
              else {
                done();
              }
            },
            removeClass: function (element, className, done) {
              // this handler is called when ng-hide is called 
              if (className == 'ng-hide') {

                // This animation is defining how we want the slide to appear when we remove the ng-hide class from an element.
                element.removeClass('ng-hide');

                // Sets the element immediately to the right of the slider by setting the left property to the width of the element�s parent. 
                TweenMax.set(element, {left: element.parent().width()});

                //A nimate on the left property to 0 so that the image slides in from right to left.
                TweenMax.to(element, 0.5, {left: 0, onComplete: done});
              }
              else {
                done();
              }
            }
          };
        });
