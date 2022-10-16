// e.g. new $$UUID().make();
// origin : https://zenn.dev/37cohina/articles/52d3c87f99cc2f

$$uuid = $$UUID = (function(){
  function MAIN(){
    // return this.data();
  }
  MAIN.prototype.make = function(){
    class UUID {
      static #uuidIte = (function*(){
        const HEXOCTETS = Object.freeze( [ ...Array( 0x100 ) ].map( ( e, i ) => i.toString( 0x10 ).padStart( 2, "0" ).toUpperCase() ) );
        const VARSION = 0x40;
        const VARIANT = 0x80;
        let bytes = null;
        if(crypto){
          bytes = crypto.getRandomValues(new Uint8Array(16));
        }
        else{
          bytes = new Uint8Array(16);
          const rand  = new Uint32Array( bytes.buffer );
          for (let i = 0; i < rand.length; i++){
            rand[ i ] = Math.random() * 0x100000000 >>> 0;
          }
        }
        yield "" +
          HEXOCTETS[ bytes[ 0 ]  ] +
          HEXOCTETS[ bytes[ 1 ]  ] +
          HEXOCTETS[ bytes[ 2 ]  ] +
          HEXOCTETS[ bytes[ 3 ]  ] + "-" +
          HEXOCTETS[ bytes[ 4 ]  ] +
          HEXOCTETS[ bytes[ 5 ]  ] + "-" +
          HEXOCTETS[ bytes[ 6 ]  & 0x0f | VARSION ] +
          HEXOCTETS[ bytes[ 7 ]  ] + "-" +
          HEXOCTETS[ bytes[ 8 ]  & 0x3f | VARIANT ] +
          HEXOCTETS[ bytes[ 9 ]  ] + "-" +
          HEXOCTETS[ bytes[ 10 ] ] +
          HEXOCTETS[ bytes[ 11 ] ] +
          HEXOCTETS[ bytes[ 12 ] ] +
          HEXOCTETS[ bytes[ 13 ] ] +
          HEXOCTETS[ bytes[ 14 ] ] +
          HEXOCTETS[ bytes[ 15 ] ];
      })();
      static randomUUID(){
        return this.#uuidIte.next().value;
      }
    }
    return UUID.randomUUID();
  };
  return MAIN;
})();
