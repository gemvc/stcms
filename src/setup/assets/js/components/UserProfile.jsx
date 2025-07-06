import React from 'react';

export default function UserProfile({ user, jwt }) {
  return (
    <div className="p-4 border rounded">
      <h2 className="font-bold">{user.name}</h2>
      <p>Email: {user.email}</p>
      {jwt && <p>JWT present!</p>}
    </div>
  );
} 