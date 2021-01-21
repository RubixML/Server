import Plotly from 'plotly.js/src/core';
import Scatter from 'plotly.js/lib/scatter';
import Bar from 'plotly.js/lib/bar';

Plotly.register([
    Scatter,
    Bar,
]);

export default Plotly;
