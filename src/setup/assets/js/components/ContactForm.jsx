import React, { useState } from 'react';

export default function ContactForm() {
    const [formData, setFormData] = useState({
        name: '', email: '', message: ''
    });
    const [submitting, setSubmitting] = useState(false);
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        // Form submission logic here
    };
    
    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <input 
                type="text" 
                placeholder="Name" 
                value={formData.name}
                onChange={(e) => setFormData({...formData, name: e.target.value})}
                className="w-full p-2 border rounded"
            />
            <button type="submit" className="bg-blue-500 text-white p-2 rounded" disabled={submitting}>
                {submitting ? 'Sending...' : 'Send Message'}
            </button>
        </form>
    );
}