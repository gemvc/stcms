import Hello from './components/Hello';
import UserProfile from './components/UserProfile';
import ContactForm from './components/ContactForm';

//each one is a React component and a function that parses the data attributes

export const registry = {
  'hello-root': { //this is the id of the DOM element to render the component into (e.g. <div id="hello-root"></div>)
    component: Hello,
    getProps: (el) => ({}), // No props needed
  },
  'user-profile-root': {
    component: UserProfile, 
    getProps: (el) => {
      // if has props (e.g. <div id="user-profile-root" data-user='{"name": "John"}' data-jwt="token"></div>)
      return {
        user: JSON.parse(el.dataset.user), //the data attributes are parsed using JSON.parse
        jwt: el.dataset.jwt,
      };
    },
  },
  'contact-form-root': {
    component: ContactForm,
    getProps: (el) => ({}),
  }
  // Add more components here as needed
};
