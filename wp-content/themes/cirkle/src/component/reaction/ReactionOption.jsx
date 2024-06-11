import React from 'react';
import app from '../../helper/core';
import plugins from '../../helper/plugins';

class ReactionOption extends React.Component {
    constructor(props) {
        super(props);

        this.reactionTooltipRef = React.createRef();
    }

    componentDidMount() {
        plugins.createTooltip({
            containerElement: this.reactionTooltipRef.current,
            offset: 4,
            direction: 'top',
            animation: {
                type: 'translate-out-fade'
            }
        });
    }

    render() {
        return (
            <div ref={this.reactionTooltipRef}
                 className="reaction-option"
                 data-title={app.capitalizeText(this.props.data.name)}
                 onClick={() => {
                     this.props.createUserReaction(this.props.data.id);
                 }}
            >
                <img className="reaction-option-image" src={this.props.data.image_url}
                     alt={`reaction-${this.props.data.name}`}/>
            </div>
        );
    }
}

export default ReactionOption;