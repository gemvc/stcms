import React, { useState, useEffect } from 'react';

export default function UserProfile({ user, jwt }) {
  const [userState, setUserState] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const userData = JSON.parse(
        document.getElementById('user-profile-root').dataset.user
    );
    setUserState(userData);
    setLoading(false);
  }, []);

  if (loading) return <div>Loading...</div>;

  return (
    <div className="p-4 bg-green-50 rounded">
      <h3 className="text-lg font-semibold mb-2">Welcome, {userState.name}!</h3>
      <p>Email: {userState.email}</p>
      <p>Role: {userState.role}</p>
      {jwt && <p>JWT present!</p>}
    </div>
  );
} 