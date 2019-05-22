import React from 'react';
import classNames from 'classnames';

class Item extends React.Component {
    render() {
        const sectionClass = classNames({
            'user-dashboard-item': true,
            ['user-dashboard-item--type-' + this.props.type]: this.props.type,
            'user-dashboard-item--has-notification': this.props.count > 0
        });
        const countClass = classNames({
            'user-dashboard-item__count': true,
            'user-dashboard-item__count--is-loading': this.props.loading
        });
        return (
            <section className={sectionClass} >
                <span className={countClass}>{this.props.count}</span>
                <div className="user-dashboard-item__text">
                    <header className="user-dashboard-item__title">{this.props.title}</header>
                    <p className="user-dashboard-item__description">{this.props.description}</p>
                </div>
            </section>
        )
    }
}

export default Item;
