import React from 'react';

const LandingPage = () => {
    return (
        <div style={{ textAlign: 'center', marginTop: '50px' }}>
            <h1>Welcome to Snake Game</h1>
            <button onClick={() => window.location.href = "/snake"}>Start Game</button>
            <button onClick={() => window.location.href = "/high-scores"}>High Scores</button>
        </div>
    );
};

export default LandingPage;
