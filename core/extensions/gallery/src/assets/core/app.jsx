import React, { Fragment } from "react";
import ReactDOM from "react-dom";
import { HashRouter, Switch, Route } from "react-router-dom";
// components
import Gallery from "./components/Gallery.jsx";
import GalleryItem from "./components/GalleryItem.jsx";
import GalleryCreate from "./components/GalleryCreate.jsx";
// app
const App = () => (
  <Fragment>
    <Switch>
      <Route exact path="/" component={Gallery} />
      <Route exact path="/create" component={GalleryCreate} />
      <Route exact path="/:name" component={GalleryItem} />
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
