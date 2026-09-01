mkdir video
cd video
mkdir multiply2
cd ..
ffmpeg  -accurate_seek -i youtube/MpsnJAExC0g.mp4 -ss 0  -vf scale=480:-1    video/multiply2/2intro.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn1134.mp4 -ss 0 -to 168   -vf scale=480:-1  video/multiply2/201.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn1134.mp4 -ss 151 -to 213   -vf scale=480:-1  video/multiply2/202.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew11223.mp4 -ss 333 -to 523   -vf scale=480:-1  video/multiply2/203.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke157240.mp4 -ss 0 -to 113   -vf scale=480:-1  concat/204.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew11223.mp4 -ss 521 -to 573   -vf scale=480:-1  concat/204-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply204.txt -c copy video/multiply2/204.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew13241412.mp4 -ss 333 -to 349   -vf scale=480:-1  video/multiply2/205.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew31425.mp4 -ss 122 -to 205   -vf scale=480:-1  video/multiply2/206.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke4144.mp4 -ss 0 -to 113   -vf scale=480:-1  video/multiply2/207.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn1134.mp4 -ss 213 -to 384   -vf scale=480:-1  concat/208.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn135222.mp4 -ss 0 -to 160   -vf scale=480:-1  concat/208-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply208.txt -c copy video/multiply2/208.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn135222.mp4 -ss 169 -to 282   -vf scale=480:-1  video/multiply2/209.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn135222.mp4 -ss 282 -to 479   -vf scale=480:-1  video/multiply2/210.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn223336.mp4 -ss 36 -to 279   -vf scale=480:-1  video/multiply2/211.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn223336.mp4 -ss 290 -to 411   -vf scale=480:-1  video/multiply2/212.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn4154.mp4 -ss 0 -to 359   -vf scale=480:-1  video/multiply2/213.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn4154.mp4 -ss 359 -to 458   -vf scale=480:-1  video/multiply2/214.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke4144.mp4 -ss 134 -to 293   -vf scale=480:-1  video/multiply2/215.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew31425.mp4 -ss 316 -to 363   -vf scale=480:-1  video/multiply2/216.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMark1145.mp4 -ss 237 -to 282   -vf scale=480:-1  video/multiply2/217.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMark1145.mp4 -ss 282 -to 355   -vf scale=480:-1  video/multiply2/218.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMark1145.mp4 -ss 355 -to 475   -vf scale=480:-1  video/multiply2/219.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke5139.mp4 -ss 0 -to 106   -vf scale=480:-1  video/multiply2/220.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke5139.mp4 -ss 116 -to 190   -vf scale=480:-1  video/multiply2/221.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke5139.mp4 -ss 192 -to 363   -vf scale=480:-1  video/multiply2/222.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke5139.mp4 -ss 377 -to 504   -vf scale=480:-1  video/multiply2/223.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn5147.mp4 -ss 0 -to 216   -vf scale=480:-1  video/multiply2/224.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn5147.mp4 -ss 194 -to 504   -vf scale=480:-1  video/multiply2/225.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke6149.mp4 -ss 125 -to 208   -vf scale=480:-1  video/multiply2/226.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke6149.mp4 -ss 208 -to 272   -vf scale=480:-1  video/multiply2/227.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke6149.mp4 -ss 269 -to 556   -vf scale=480:-1  video/multiply2/228.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke7150.mp4 -ss 1 -to 115   -vf scale=480:-1  video/multiply2/229.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke7150.mp4 -ss 196 -to 381   -vf scale=480:-1  video/multiply2/230.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke7150.mp4 -ss 387 -to 568   -vf scale=480:-1  concat/231.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke8139.mp4 -ss 0 -to 33   -vf scale=480:-1  concat/231-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply231.txt -c copy video/multiply2/231.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMark320441.mp4 -ss 478 -to 573   -vf scale=480:-1  video/multiply2/232.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMark5143.mp4 -ss 1 -to 180   -vf scale=480:-1  video/multiply2/233.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke840917.mp4 -ss 213 -to 262   -vf scale=480:-1  concat/234.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew10142.mp4 -ss 43 -to 193   -vf scale=480:-1  concat/234-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply234.txt -c copy video/multiply2/234.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew13241412.mp4 -ss 378 -to 467   -vf scale=480:-1  concat/235.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew13241412.mp4 -ss 284 -to 290   -vf scale=480:-1  concat/235-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply235.txt -c copy video/multiply2/235.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn6160.mp4 -ss 0 -to 125   -vf scale=480:-1  video/multiply2/236.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew14131520.mp4 -ss 95 -to 211   -vf scale=480:-1  video/multiply2/237.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMark71821.mp4 -ss 265 -to 457   -vf scale=480:-1  video/multiply2/238.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew1621189.mp4 -ss 376 -to 452   -vf scale=480:-1  concat/239.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew1621189.mp4 -ss 0 -to 136   -vf scale=480:-1  concat/239-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply239.txt -c copy video/multiply2/239.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke91862.mp4 -ss 123 -to 221   -vf scale=480:-1  video/multiply2/240.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn661744.mp4 -ss 97 -to 496   -vf scale=480:-1  concat/241.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn9141.mp4 -ss 0 -to 220   -vf scale=480:-1  concat/241-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply241.txt -c copy video/multiply2/241.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke10142.mp4 -ss 0 -to 423   -vf scale=480:-1  video/multiply2/242.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke10142.mp4 -ss 0 -to 471   -vf scale=480:-1  video/multiply2/243.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke19144.mp4 -ss 0 -to 120   -vf scale=480:-1  video/multiply2/244.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke19144.mp4 -ss 1 -to 225   -vf scale=480:-1  video/multiply2/245.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke19144.mp4 -ss 225 -to 403   -vf scale=480:-1  concat/246.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke19452047.mp4 -ss 1 -to 63   -vf scale=480:-1  concat/246-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply246.txt -c copy video/multiply2/246.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn13138.mp4 -ss 0  -vf scale=480:-1    video/multiply2/247.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn14131.mp4 -ss 0  -vf scale=480:-1    concat/248.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew263675.mp4 -ss 0 -to 115   -vf scale=480:-1  concat/248-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply248.txt -c copy video/multiply2/248.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn19437.mp4 -ss 111 -to 425   -vf scale=480:-1  concat/249.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew27322820.mp4 -ss 146 -to 175   -vf scale=480:-1  concat/249-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply249.txt -c copy video/multiply2/249.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn19437.mp4 -ss 459 -to 535   -vf scale=480:-1  concat/250.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn19437.mp4 -ss 0  -vf scale=480:-1    concat/250-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply250.txt -c copy video/multiply2/250.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn21125.mp4 -ss 0  -vf scale=480:-1    video/multiply2/251.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew27322820.mp4 -ss 522 -to 569   -vf scale=480:-1  video/multiply2/252.mp4
