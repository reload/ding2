import React from 'react';
import { render } from 'react-dom';
import UserDashboard from './UserDashboard.js';
import './UserDashboard.css';

const root = document.getElementById('ding-user-frontend-js-user-dashboard');
render(
  <UserDashboard token={root.dataset.apiToken} />,
  root
);
