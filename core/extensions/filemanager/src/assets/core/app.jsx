import React, { Fragment } from "react";
import ReactDOM from "react-dom";
import { HashRouter, Switch, Route } from "react-router-dom";

// components
import Root from "./components/Root.jsx";
import Rename from "./components/Rename.jsx";

// app
const App = () => (
  <Fragment>
    <Switch>
      <Route exact path="/" component={Root} />
      <Route exact path="/rename/:name/:ext" component={Rename} />
    </Switch>
  </Fragment>
);
// render router
ReactDOM.render(
  <HashRouter>
    <App />
  </HashRouter>,
  window.root
);
