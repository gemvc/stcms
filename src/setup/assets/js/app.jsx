import React from 'react';
import { createRoot } from 'react-dom/client';
import { registry } from './registry';

/*Important: 
1. do not change this file, it is automatically detect all components in the registry.js file and mount them
2. all new components must be added to the registry.js file
3. after add new component you shall npm run build!
*/
Object.entries(registry).forEach(([id, { component: Component, getProps }]) => {
  const el = document.getElementById(id);
  if (el) {
    const props = getProps ? getProps(el) : {};
    createRoot(el).render(<Component {...props} />);
  }
});