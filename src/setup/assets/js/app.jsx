import React from 'react';
import { createRoot } from 'react-dom/client';
import UserProfile from './components/UserProfile';

const el = document.getElementById('user-profile-root');
if (el) {
  const user = JSON.parse(el.dataset.user);
  const jwt = el.dataset.jwt;
  createRoot(el).render(<UserProfile user={user} jwt={jwt} />);
} 