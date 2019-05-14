import React from 'react';
import ReactDOM from 'react-dom';
import UserDashboard from './components/Dashboard/UserDashboard';

it('renders without crashing', () => {
  const div = document.createElement('div');
  ReactDOM.render(<UserDashboard />, div);
  ReactDOM.unmountComponentAtNode(div);
});
