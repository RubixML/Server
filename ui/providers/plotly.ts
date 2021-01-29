import Plotly from 'plotly.js/src/core';
import Scatter from 'plotly.js/src/traces/scatter';
import Bar from 'plotly.js/src/traces/bar';

Plotly.register([
    Scatter,
    Bar,
]);

export default Plotly;
