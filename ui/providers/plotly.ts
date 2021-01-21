import Plotly from 'plotly.js/src/core';
import Scatter from 'plotly.js/src/traces/scatter';
import ScatterGL from 'plotly.js/src/traces/scattergl';
import Bar from 'plotly.js/src/traces/bar';

Plotly.register([
    Scatter,
    ScatterGL,
    Bar,
]);

export default Plotly;
