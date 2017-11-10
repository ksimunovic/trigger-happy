import React from 'react';
import { CookiesProvider, withCookies, Cookies } from 'react-cookie';

const ListenState = {
    NOT_LISTENING: 0,
    LISTENING: 1,
    FINISHED: 2
}
class HookListener extends React.Component {
    constructor(props){
        super(props);
        this.state = { listeningState: ListenState.NOT_LISTENING, hooks: [] };
        this.stopListening = this.stopListening.bind(this);
    }
    componentWillUnmount() {
        const { cookies } = this.props;
        cookies.remove("listenHooks");
    }

    selectDynamicHook(c) {
		this.setState({ selectedDynamicHook: c});
	 }
     doListen(force) {
        const { cookies } = this.props;
        let date = new Date();
  		date.setTime(date.getTime() + 2000); // 2 seconds
        if (force == undefined  && this.state.listeningState !== ListenState.LISTENING) {
            cookies.remove("listenHooks", { path: '/' });
            return;
        }
        cookies.set("listenHooks",true, { path: "/", expires: date });
        var self = this;
 		fetch('?testf=1').then(function(response) {

 			return response.json();

 		}).then(function(hooks) {
 			if (hooks.length == 0) {
 				self.timeout = setTimeout(()=>self.doListen(),500);
 				return;
 			}
 			self.setState({foundHooks: hooks, foundHooksCount: self.countHooks(hooks) });
            self.timeout = setTimeout(()=>self.doListen(),500);
 		});
 	}
    countHooks(hooks) {
        var t = [];
        for (var h in hooks) {
            var h1 = hooks[h];
            if (!t.indexOf(h1.name))
            t.push(h1.name);
        }
        return t.length + 6;
    }
    startListening() {
        this.setState({ listeningState: ListenState.LISTENING });

 		this.doListen(true);
 	}
    stopListening() {
        clearTimeout(this.timeout);
        const { cookies } = this.props;
        this.setState({ listeningState: ListenState.FINISHED });
        c
 	}
	renderHooks(hooks) {
		var results = [];
		let selected = [];
		if (this.state.selectedDynamicHook) {
			selected = JSON.stringify(this.state.selectedDynamicHook);
		}
		for (var h in hooks) {
			let children = hooks[h].map(c=> <div onClick={()=>this.selectDynamicHook(c)}>{c.name}</div>);

			results.push(<li><strong>{h}</strong><ul>{children}</ul></li>);
		}
		return <div>Selected: {selected}<ul>{results}</ul></div>;
	}
    render() {
		let selectPlugin = this.selectPlugin;

            let lstate = this.state.listeningState;
            let children = [];
            if (lstate == ListenState.NOT_LISTENING) {
                // Show "Start Listening" button
                children.push(<div>
                <a onClick={()=>this.startListening()} className="listen-button">Start Hook Detection</a>
                </div>);
            } else if (lstate == ListenState.LISTENING) {
                children.push(<div>

                    <div><p>Hook detection in progress. Navigate to a page on your site to retrieve a list of available hooks for that page</p>
                    <p><h4>Found {(this.state.foundHooksCount||0)} hooks</h4></p></div>
                <a onClick={()=>this.stopListening()} className="listen-button">Stop Listening</a>
                </div>);
                // Show Progress
            } else if (lstate == ListenState.FINISHED) {
                // Show list of hooks
                let hooks = this.state.foundHooks ? this.renderHooks(this.state.foundHooks) : <div>Searching...</div>;
                children.push(<div>
                    <h5>Hooks found:</h5>
        			{ hooks }
                </div>);
            }

			return (<div className="listen-hook-dialog">
			<h4 className="listen-hook-dialog__title">Custom Hooks</h4>
			{children}

			</div>);


    }
}
export default withCookies(HookListener);
