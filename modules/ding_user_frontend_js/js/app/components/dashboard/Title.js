import React from 'react';
import classNames from 'classnames';

class Title extends React.Component {
    render() {
        const countClass = classNames({
            'user-dashboard-title__count': true,
            'user-dashboard-title__count--is-loading': this.props.loading
        });

        return (
            <header className="user-dashboard-title">
                {this.props.text}
                <span className={countClass}>{this.props.count}</span>
            </header>
        )
    };
}

export default Title;
