import React, { Fragment, useState, useEffect } from "react";
import { Link } from "react-router-dom";

import { fetchData, Row, Col, Card } from "./utils.jsx";

const Gallery = () => {
  const [data, setData] = useState([]);

  useEffect(() => {
    let url = `${site_url}/api/gallery/all`;
    fetchData(url).then((r) => setData(r));
  }, []);

  // delete gallery method
  const deleteGallery = (evt, name, uid) => {
    evt.preventDefault();
    if (confirm("Are you sure to delete gallery")) {
      let url = `${site_url}/gallery/delete/${name}/${uid}`;
      fetchData(url).then((r) => {
        message(r.message);
        let url = `${site_url}/api/gallery/all`;
        fetchData(url).then((r) => setData(r));
      });
    }
    return false;
  };
  return (
    <Fragment>
      <Row>
        <Col num="12">
          <Link to="/create" className="btn btn-sm btn-outline-primary rounded-0 mr-2">
            <i className="fa fa-plus mr-2"></i> new
          </Link>
          <a
            title="Preview Json gallery"
            className="btn btn-sm btn-outline-info rounded-0"
            target="_blank"
            href={`${site_url}/api/gallery/all`}>
            <i className="fa fa-code"></i></a>
        </Col>
      </Row>
      <Row>
        {data
          ? data.map((item) => (
              <Col num="4" key={item.uid}>
                <Card
                  data={item}
                  fn={(evt) => deleteGallery(evt, item.name, item.uid)}
                />
              </Col>
            ))
          : "No galleries create new"}
      </Row>
    </Fragment>
  );
};

export default Gallery;
