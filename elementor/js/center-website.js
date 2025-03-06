const DefaultDeviceWidths = {
  default: 1260,
  tablet: 1024,
  mobile: 540,
};

function getCurrentDevice() {
  let currentDevice = 'default';
  const width = window.innerWidth;
  if (width <= DefaultDeviceWidths.mobile) {
    currentDevice = 'mobile';
  } else if (width <= DefaultDeviceWidths.tablet) {
    currentDevice = 'tablet';
  }
  return currentDevice;
}

function generateScreenResponsiveNumberUnitValues(values) {
  const newValues = { ...values };
  const ratio = Number(values.default?.value) / DefaultDeviceWidths.default;

  if (!values.mobile) {
    if (!values.tablet) {
      newValues.mobile = {
        value: ratio * DefaultDeviceWidths.mobile,
        unit: values.default?.unit,
      };
    } else {
      const tabletRatio = Number(values.tablet?.value) / DefaultDeviceWidths.tablet;

      newValues.mobile = {
        value: tabletRatio * DefaultDeviceWidths.mobile,
        unit: values.default?.unit,
      };
    }
  }
  if (!values.tablet) {
    newValues.tablet = {
      value: ratio * DefaultDeviceWidths.tablet,
      unit: values.default?.unit,
    };
  }

  return newValues;
};

function generateScreenResponsivePaddingValues(values) {
  const newValues = { ...values };

  const defaultTopRatio = Number(values.default?.top) / DefaultDeviceWidths.default;
  const defaultRightRatio = Number(values.default?.right) / DefaultDeviceWidths.default;
  const defaultBottomRatio = Number(values.default?.bottom) / DefaultDeviceWidths.default;
  const defaultLeftRatio = Number(values.default?.left) / DefaultDeviceWidths.default;

  if (!values.mobile) {
    if (!values.tablet) {
      newValues.mobile = {
        top: defaultTopRatio * DefaultDeviceWidths.mobile,
        right: defaultRightRatio * DefaultDeviceWidths.mobile,
        bottom: defaultBottomRatio * DefaultDeviceWidths.mobile,
        left: defaultLeftRatio * DefaultDeviceWidths.mobile,
      };
    } else {
      const tabletTopRatio = Number(values.tablet?.top) / DefaultDeviceWidths.tablet;
      const tabletRightRatio = Number(values.tablet?.right) / DefaultDeviceWidths.tablet;
      const tabletBottomRatio = Number(values.tablet?.bottom) / DefaultDeviceWidths.tablet;
      const tabletLeftRatio = Number(values.tablet?.left) / DefaultDeviceWidths.tablet;

      newValues.mobile = {
        top: tabletTopRatio * DefaultDeviceWidths.mobile,
        right: tabletRightRatio * DefaultDeviceWidths.mobile,
        bottom: tabletBottomRatio * DefaultDeviceWidths.mobile,
        left: tabletLeftRatio * DefaultDeviceWidths.mobile,
      };
    }
  }

  if (!values.tablet) {
    newValues.tablet = {
      top: defaultTopRatio * DefaultDeviceWidths.tablet,
      right: defaultRightRatio * DefaultDeviceWidths.tablet,
      bottom: defaultBottomRatio * DefaultDeviceWidths.tablet,
      left: defaultLeftRatio * DefaultDeviceWidths.tablet,
    };
  }

  return newValues;
};

function generateScreenResponsiveOffsetValues(values) {
  const newValues = { ...values };

  const defaultXRatio = Number(values.default?.x) / DefaultDeviceWidths.default;
  const defaultYRatio = Number(values.default?.y) / DefaultDeviceWidths.default;

  if (!values.mobile) {
    if (!values.tablet) {
      newValues.mobile = {
        x: defaultXRatio * DefaultDeviceWidths.mobile,
        y: defaultYRatio * DefaultDeviceWidths.mobile,
      };
    } else {
      const tabletXRatio = Number(values.tablet?.x) / DefaultDeviceWidths.tablet;
      const tabletYRatio = Number(values.tablet?.y) / DefaultDeviceWidths.tablet;

      newValues.mobile = {
        x: tabletXRatio * DefaultDeviceWidths.mobile,
        y: tabletYRatio * DefaultDeviceWidths.mobile,
      };
    }
  }
  if (!values.tablet) {
    newValues.tablet = {
      x: defaultXRatio * DefaultDeviceWidths.tablet,
      y: defaultYRatio * DefaultDeviceWidths.tablet,
    };
  }
  return newValues;
}

function generateScreenResponsivePositionValues(values) {
  const newValues = values || {
    default: { horizontal: 'left', vertical: 'top' }
  };

  if (!values.mobile) {
    if (!values.tablet) {
      newValues.mobile = {
        horizontal: values.default?.horizontal || 'left',
        vertical: values.default?.vertical || 'top'
      };
    } else {
      newValues.mobile = {
        horizontal: values.tablet?.horizontal || 'left',
        vertical: values.tablet?.vertical || 'top'
      };
    }
  }

  if (!values.tablet) {
    newValues.tablet = {
      horizontal: values.default?.horizontal || 'left',
      vertical: values.default?.vertical || 'top'
    };
  }

  return newValues;
}

function generateScreenResponsiveValues(values) {
  const newValues = { ...values };

  if (values.mobile === undefined) {
    if (values.tablet === undefined) {
      newValues.mobile = values.default;
    } else {
      newValues.mobile = values.tablet;
    }
  }
  if (values.tablet === undefined) {
    newValues.tablet = values.default;
  }

  return newValues;
}

