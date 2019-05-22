import React, { useState, useEffect } from 'react';
import { isPast, isFuture, differenceInDays } from 'date-fns';
import Title from './Title';
import Item from './Item';

const API_URL = 'http://www.mocky.io/v2/5cda8e37300000680068c7cc?mocky-delay=5000ms';

function UserDashboard(props) {
  const [data, setData] = useState({loans: [], orders: [], isFetching: false});

  useEffect(() => {
    const fetchData = async () => {
      try {
        setData({loans: data.loans, orders: data.orders, isFetching: true});
        const response = await fetch(API_URL).then(response => response.json());
        setData({loans: response['data.json'].loans, orders: response['data.json'].orders, isFetching: false});
      } catch (e) {
        console.log(e);
        setData({loans: data.loans, orders: data.orders, isFetching: false});
      }
    };
    fetchData();
  }, []);

  const numLoans = data.loans.length;
  const numLoansOverdue = (data.loans.filter((loan => isPast(new Date(loan.dueDate))))).length;
  // Loans are due soon when within 1 week of their due date.
  const numLoansDueSoon = (data.loans.filter(loan => isFuture(new Date(loan.dueDate)) && differenceInDays(new Date(loan.dueDate), new Date()) > 7)).length;
  const numOtherLoans = numLoans - numLoansOverdue - numLoansDueSoon;

  const numOrders = data.orders.length;
  const numOrdersPickup = (data.orders.filter(order => order.status === 'ready')).length;
  const numOtherOrders = numOrders - numOrdersPickup;

  return (
    <div className="user-dashboard">
      <section className="user-dashboard__column">
        <Title text={"Lån"} loading={data.isFetching} count={numLoans}/>
        <Item type="loans-overdue" title={"Overskredet"} description={"Lån har overskredet afleveringsdatoen"} loading={data.isFetching} count={numLoansOverdue}/>
        <Item type="loans-due" title={"Udløber snart"} description={"Lån udløber snart"} loading={data.isFetching} count={numLoansDueSoon} />
        <Item type="loans-other" title={"Dine lån"} description={"Lån med længere udløbstid"} loading={data.isFetching} count={numOtherLoans} />
      </section>
      <section className="user-dashboard__column">
        <Title text={"Reserveringer"} loading={data.isFetching} count={numOrders}/>
        <Item type="orders-ready" title={"Klar til afhentning"} description={"Reserveringer kan nu afhentes på biblioteket"} loading={data.isFetching} count={numOrdersPickup} />
        <Item type="orders-other" title={"Du står i kø"} description={"Reserveringer du står i kø til"} loading={data.isFetching} count={numOtherOrders} />
      </section>
    </div>
  );
}

export default UserDashboard;
