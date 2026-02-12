//
//  ImageListCVC.swift
//  Dabada
//
//  Created by flower on 2/9/18.
//  Copyright © 2018 Smith. All rights reserved.
//

import UIKit

class ImageListCVC: UICollectionViewCell {

    @IBOutlet weak var imgPhoto: UIImageView!
    @IBOutlet weak var vwEffect: UIView!
    @IBOutlet weak var lbNumber: UILabel!
    
    var representedAssetIdentifier : String!
    
    override func awakeFromNib() {
        super.awakeFromNib()
    }

}
