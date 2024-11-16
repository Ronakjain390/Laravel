import Datepicker from 'flowbite-datepicker/Datepicker';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken } from 'firebase/messaging';
import 'flowbite';
import 'daisyui';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
import 'sweetalert2/dist/sweetalert2.css';
import Swal from 'sweetalert2';
// resources/js/app.js


// import './../../vendor/power-components/livewire-powergrid/dist/powergrid.js';
// import './../../vendor/power-components/livewire-powergrid/dist/powergrid.css'; // Corrected path
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import Alpine from 'alpinejs';

// import invoiceComponent from './components/invoiceComponent.js';

// // Register Alpine.js components
// document.addEventListener('alpine:init', () => {
//     Alpine.data('invoiceComponent', invoiceComponent);  // Register the invoiceComponent
// });

import { invoiceComponent } from './components/invoiceComponent.js';  // Import the component

// Register Alpine component on page load
document.addEventListener('alpine:init', () => {
    Alpine.data('invoiceComponent', invoiceComponent);  // Register the component
});

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();


// Initialize Flowbite components
function initFlowbiteComponents() {
    document.querySelectorAll('[data-dropdown-toggle]').forEach(function (dropdownToggle) {
        const dropdown = document.getElementById(dropdownToggle.getAttribute('data-dropdown-toggle'));
        if (dropdown) {
            dropdownToggle.addEventListener('click', function () {
                dropdown.classList.toggle('hidden');
            });
        }
    });
}

// Initialize Firebase
const firebaseConfig = {
    // Your Firebase config
};
const app = initializeApp(firebaseConfig);

// Add SweetAlert2 to the global scope
window.Swal = Swal;

// Initialize NProgress
NProgress.configure({ showSpinner: false });

NProgress.configure({ showSpinner: false });

  document.addEventListener('livewire:load', () => {
      Livewire.hook('message.sent', () => {
          NProgress.start();
      });

      Livewire.hook('message.processed', () => {
          NProgress.done();
      });
  });

  document.addEventListener('alpine:init', () => {
      Alpine.data('app', () => ({
          handleLinkClick(event) {
              const link = event.target.closest('a');
              if (link && link.href && !link.href.startsWith('#') && !link.hasAttribute('download') && !link.target) {
                  NProgress.start();
              }
          },
      }));
  });

  window.addEventListener('load', () => {
      NProgress.done();
  });

  window.addEventListener('beforeunload', () => {
      NProgress.start();
  });





  function requestPermission() {
    console.log('Requesting permission...');
    Notification.requestPermission().then((permission) => {
      if (permission === 'granted') {
        console.log('Notification permission granted.');
        // TODO(developer): Retrieve a registration token for use with FCM.
        // ..
        getToken(getMessaging(app), { vapidKey: 'BJyr0u7tQUe3vJgXHOVy5sPRcEwu2qSKv1m6_BWZnS32Br0NUiz7z9QkalvErrtOEdrW-0um9GbDuXbxkMMqIOc' }).then((currentToken) => {
          if (currentToken) {
            // Send the token to your server and update the UI if necessary
            console.log('Token:', currentToken);

            // Send the token to your server
            fetch('/save-fcm-token', {
              method: 'post',
              headers: {
                'Content-Type': 'application/json',
              },
            body: JSON.stringify({ fcm_token: currentToken }),
            }).then(response => {
              if (!response.ok) {
                throw new Error('Network response was not ok');
              }
              return response.json();
            }).then(data => {
              console.log('Success:', data);
            }).catch((error) => {
              console.error('Error:', error);
            });

            // ...
          } else {
            // Show permission request UI
            console.log('No registration token available. Request permission to generate one.');
            // ...
          }
        }).catch((err) => {
          console.log('An error occurred while retrieving token. ', err);
          // ...
        });
      } else {
        console.log('Unable to get permission to notify.');
      }
    });
  }


 function initializeEmailValidation() {
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email_error');
    const addButton = document.getElementById('add');

    if (emailInput) {
        emailInput.addEventListener('input', function () {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailInput.value && !emailPattern.test(emailInput.value)) {
                emailError.textContent = 'Invalid email address.';
                emailInput.classList.add('border-red-500');
                addButton.disabled = true;
                addButton.classList.add('cursor-not-allowed');
                addButton.classList.add('opacity-50');
            } else {
                emailError.textContent = '';
                emailInput.classList.remove('border-red-500');
                addButton.disabled = false;
                addButton.classList.remove('cursor-not-allowed');
                addButton.classList.remove('opacity-50');
            }
        });
    }
}

function initializePhoneValidation() {
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phone_error');
    const addButton = document.getElementById('add');

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            const phonePattern = /^\d{10}$/;
            if (phoneInput.value && !phonePattern.test(phoneInput.value)) {
                phoneError.textContent = 'Phone number must be 10 digits.';
                phoneInput.classList.add('border-red-500');
                addButton.disabled = true;
                addButton.classList.add('cursor-not-allowed');
                addButton.classList.add('opacity-50');
            } else {
                phoneError.textContent = '';
                phoneInput.classList.remove('border-red-500');
                addButton.disabled = false;
                addButton.classList.remove('cursor-not-allowed');
                addButton.classList.remove('opacity-50');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    initializeEmailValidation();
    initializePhoneValidation();
});

document.addEventListener('livewire:load', function () {
    initializeEmailValidation();
    initializePhoneValidation();
});

document.addEventListener('livewire:update', function () {
    initializeEmailValidation();
    initializePhoneValidation();
});

  // const messaging = getMessaging();
  // getToken(messaging, { vapidKey: 'BJEsDKYH-PERKTy6MV7S2sI2Fbbd8g3yqCuHAdEhgXh5o3SwK9TQJbb_7qx1aVz_a66NJ4YoZl8DZQiwSuiAkbM' }).then((currentToken) => {
  //   if (currentToken) {
  //     // Send the token to your server and update the UI if necessary
  //     console.log('Token:', currentToken);
  //     // ...
  //   } else {
  //     // Show permission request UI
  //     console.log('No registration token available. Request permission to generate one.');
  //     // ...
  //   }
  // }).catch((err) => {
  //   console.log('An error occurred while retrieving token. ', err);
  //   // ...
  // });
  // const messaging = firebase.messaging();
// messaging.getToken().then((token) => {
//     console.log('FCM token:', token);
// }).catch((error) => {
//     console.error('Error getting FCM token:', error);
// });

        // Initialize Firebase
        // const app = initializeApp(firebaseConfig);

        // const messaging = getMessaging();


        // messaging.getToken({vapidKey: 'BJEsDKYH-PERKTy6MV7S2sI2Fbbd8g3yqCuHAdEhgXh5o3SwK9TQJbb_7qx1aVz_a66NJ4YoZl8DZQiwSuiAkbM'}).then((refreshedToken) => {
        //     console.log('Token refreshed.');
        //     // Send the new token to your server
        //     fetch('/save-fcm-token', {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //         },
        //         body: JSON.stringify({fcm_token: refreshedToken}),
        //     })
        //     .then(response => {
        //         if (!response.ok) {
        //             throw new Error('Network response was not ok');
        //         }
        //         return response.json();
        //     })
        //     .then(data => console.log('Token saved successfully:', data))
        //     .catch((error) => {
        //         console.error('There has been a problem with your fetch operation:', error);
        //     });
        // }).catch((err) => {
        //     console.log('Unable to retrieve refreshed token ', err);
        // });

// Get FCM token
// getToken(messaging, {vapidKey: 'BJEsDKYH-PERKTy6MV7S2sI2Fbbd8g3yqCuHAdEhgXh5o3SwK9TQJbb_7qx1aVz_a66NJ4YoZl8DZQiwSuiAkbM'}).then((currentToken) => {
//     if (currentToken) {
//       console.log('FCM Token:', currentToken);
//       // Send the token to your server for use
//     } else {
//       console.log('Failed to get FCM token');
//     }
//   }).catch((err) => {
//     console.log('An error occurred while retrieving token. ', err);
//   });
// {"publicKey":"BJEsDKYH-PERKTy6MV7S2sI2Fbbd8g3yqCuHAdEhgXh5o3SwK9TQJbb_7qx1aVz_a66NJ4YoZl8DZQiwSuiAkbM","privateKey":"v7xZmh84L9dPO1mrlvX3orcgJv3L-NYBwGFlAuC-86U"}
// {"publicKey":"BAC0pn2yjlrg3fWmxpvbs21F4UShcpYI0eq9Eo2oVIQcc1_mynfg3BfVVJwh1oQc6OGeqYtNGAIILCuZ4VPvToI","privateKey":"T_X16VryTEkSfVJqmJldcQtJD6OlBVjgMOVWLsxUuXk"}
