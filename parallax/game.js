var game;
window.onload = function(){
  let gameConfig = {
    type: Phaser.CANVAS,
    width: 1900,
    height: 1000,
    pixelArt: true,
    physics: {
      default: "arcade",
      arcade: {
          gravity: {
            y: 0
          }
      }
    },
    scene: [preloadGame, playGame]
  }
  game = new Phaser.Game(gameConfig);
}
