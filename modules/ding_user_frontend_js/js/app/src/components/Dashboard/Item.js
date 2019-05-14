import React from 'react';

class Item extends React.Component {
    render() {
        return (
            <section className="user-dashboard-item user-dashboard-item--has-notification">
                <span className="user-dashboard-item__count">{this.props.count}</span>
                <div className="user-dashboard-item__text">
                <header className="user-dashboard-item__title">{this.props.title}</header>
                <p className="user-dashboard-item__description">{this.props.description}</p>
                </div>
            </section>
        )
    }
}

export default Item;
