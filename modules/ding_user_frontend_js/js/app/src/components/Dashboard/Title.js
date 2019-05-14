import React from 'react';

class Title extends React.Component {
    render() {
        return (
            <header className="user-dashboard-title">
                {this.props.text}
                <span className="user-dashboard-title__count">{this.props.count}</span>
            </header>
        )
    };
}

export default Title;
