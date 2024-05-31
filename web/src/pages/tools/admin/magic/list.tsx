import React, { useRef } from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Tag, Form } from 'tdesign-react/esm'
import {
  CreateButton,
  DeleteLink,
  EditLink,
  FilterSider,
  PageTable,
  TableRef,
} from '@duxweb/dux-refine'
import { Icon } from 'tdesign-icons-react'

const List = () => {
  const translate = useTranslate()
  const table = useRef<TableRef>(null)

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'group_label',
        title: translate('tools.magic.fields.group'),
        ellipsis: true,
        cell: ({ row }) => {
          return (
            <div className='flex items-center gap-2'>
              <Icon name={row.group_icon} />{' '}
              {row.group_label || translate('tools.magic.fields.unknown')}
            </div>
          )
        },
      },
      {
        colKey: 'label',
        title: translate('tools.magic.fields.label'),
        ellipsis: true,
      },
      {
        colKey: 'type',
        title: translate('tools.magic.fields.type'),
        ellipsis: true,
        cell: ({ row }) => {
          return (
            <>
              {row.type === 'common' && (
                <Tag variant='outline' theme='primary'>
                  {translate('tools.magic.fields.list')}
                </Tag>
              )}
              {row.type === 'pages' && (
                <Tag variant='outline' theme='warning'>
                  {translate('tools.magic.fields.pages')}
                </Tag>
              )}
              {row.type === 'tree' && (
                <Tag variant='outline' theme='success'>
                  {translate('tools.magic.fields.tree')}
                </Tag>
              )}
              {row.type === 'page' && (
                <Tag variant='outline' theme='success'>
                  {translate('tools.magic.fields.page')}
                </Tag>
              )}
            </>
          )
        },
      },
      {
        colKey: 'external',
        title: translate('tools.magic.fields.external'),
        ellipsis: true,
        cell: ({ row }) => {
          const external = row.external as string[]
          return (
            <div className='flex gap-2'>
              {!external ? '-' : ''}
              {external?.indexOf?.('read') ? (
                <Tag variant='outline' theme='primary'>
                  {translate('tools.magic.external.read')}
                </Tag>
              ) : (
                ''
              )}
              {external?.indexOf?.('create') ? (
                <Tag variant='outline' theme='primary'>
                  {translate('tools.magic.external.create')}
                </Tag>
              ) : (
                ''
              )}
              {external?.indexOf?.('edit') ? (
                <Tag variant='outline' theme='primary'>
                  {translate('tools.magic.external.edit')}
                </Tag>
              ) : (
                ''
              )}
            </div>
          )
        },
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 120,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <EditLink rowId={row.id} />
              <DeleteLink rowId={row.id} />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )
  const [form] = Form.useForm()

  return (
    <PageTable
      ref={table}
      columns={columns}
      filterForm={form}
      actionRender={() => <CreateButton />}
      siderRender={() => (
        <FilterSider
          title={translate('tools.magic.fields.group')}
          component={() => import('./group')}
          resource='tools.magicGroup'
          field='group_id'
          optionLabel='label'
          optionValue='id'
        />
      )}
    />
  )
}

export default List
