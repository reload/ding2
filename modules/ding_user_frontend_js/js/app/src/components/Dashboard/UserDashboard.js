import React, { useState, useEffect } from 'react';
import '../../css/UserDashboard.css';
import Title from './Title';
import Item from './Item';

function UserDashboard() {
  return (
    <div className="user-dashboard">
      <section className="user-dashboard__column">
        <Title text={"Lån"} count={12}/>
        <Item title={"Overskredet"} description={"Lån har overskredet afleveringsdatoen"} count={1}/>
        <Item title={"Udløber snart"} description={"Lån udløber snart"} count={2} />
        <Item title={"Dine lån"} description={"Lån med længere udløbstid"} count={9} />
      </section>
      <section className="user-dashboard__column">
        <Title text={"Reserveringer"} count={7}/>
        <Item title={"Klar til afhentning"} description={"Reserveringer kan nu afhentes på biblioteket"} count={1} />
        <Item title={"Du står i kø"} description={"Reserveringer du står i kø til"} count={0} />
      </section>
    </div>
  );
}

export default UserDashboard;
