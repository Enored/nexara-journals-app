import React from 'react';
import { createRoot } from 'react-dom/client';
import '../css/journal-home.css';
import App from './journal-ui/app';

const rootElement = document.getElementById('root');

if (rootElement) {
  document.documentElement.dataset.type = 'newsreader';
  document.documentElement.dataset.density = 'regular';
  createRoot(rootElement).render(<App />);
}
